<?php

namespace App\Http\Controllers;

use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\UnlistedLead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_FAILS = 5;

    // Lockout durations per lockout_count: 1st=15min, 2nd=30min, 3rd=1hr, 4th=6hr, 5th+=24hr
    private const LOCKOUT_SCHEDULE = [15, 30, 60, 360, 1440];

    private const OTP_TTL_MINUTES     = 10;
    private const OTP_MAX_ATTEMPTS    = 5;
    private const OTP_VERIFIED_WINDOW = 15; // minutes the verified-email session flag stays valid

    // ─── Lockout helpers (query-free — work on an already-loaded $user) ──

    private function isLocked(User $user): bool
    {
        return $user->login_locked_until !== null
            && $user->login_locked_until->isFuture();
    }

    private function lockoutMinutes(User $user): int
    {
        $index = min($user->lockout_count, count(self::LOCKOUT_SCHEDULE) - 1);
        return self::LOCKOUT_SCHEDULE[$index];
    }

    private function recordFailure(User $user): void
    {
        $attempts = $user->failed_login_attempts + 1;
        $update   = ['failed_login_attempts' => $attempts];

        if ($attempts >= self::MAX_FAILS) {
            $minutes                      = $this->lockoutMinutes($user);
            $update['login_locked_until'] = now()->addMinutes($minutes);
            $update['lockout_count']      = $user->lockout_count + 1;
            $update['failed_login_attempts'] = 0; // reset counter for next lockout cycle
        }

        $user->update($update);
    }

    private function clearFailures(User $user): void
    {
        $user->update([
            'failed_login_attempts' => 0,
            'login_locked_until'    => null,
            'lockout_count'         => 0,   // full reset on successful login
        ]);
    }

    // ─── Register ────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        // Honeypot — bots fill hidden fields, humans don't
        if ($request->filled('_hp')) {
            return response()->json(['success' => true, 'redirect' => url('/')]);
        }

        try {
            $request->validate([
                'name'               => 'required|string|max:100',
                'email'              => 'required|email|unique:users,email',
                'phone'              => 'required|digits:10|unique:users,phone',
                'password'           => 'required|min:6',
                'unlisted_user_type' => 'required|in:unlisted,channel_partner',
            ], [
                'name.required'               => 'Full name is required.',
                'email.required'              => 'Email address is required.',
                'email.email'                 => 'Please enter a valid email address.',
                'email.unique'                => 'This email is already registered.',
                'phone.required'              => 'Phone number is required.',
                'phone.digits'                => 'Phone number must be exactly 10 digits.',
                'phone.unique'                => 'This phone number is already registered.',
                'password.required'           => 'Password is required.',
                'password.min'                => 'Password must be at least 6 characters.',
                'unlisted_user_type.required' => 'Please select a user type.',
                'unlisted_user_type.in'       => 'Invalid user type selected.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $email        = strtolower(trim($request->email));
        $verifiedUntil = session('otp_verified_until');

        if (session('otp_verified_email') !== $email || !$verifiedUntil || now()->gt($verifiedUntil)) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email with the OTP sent to you before signing up.',
            ], 422);
        }

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name'               => strip_tags($request->name),
                'email'              => $request->email,
                'phone'              => $request->phone,
                'password'           => $request->password,
                'user_type'          => 'member',
                'unlisted_user_type' => $request->unlisted_user_type,
                'session_token'      => Str::random(60),
            ]);

            $now = now();
            UnlistedLead::create([
                'UL_LEAD_UID'                        => $user->uid,
                'UL_LEAD_TYPE'                       => $request->unlisted_user_type,
                'UL_LEAD_INSERT_TIME'                => $now,
                'UL_LEAD_UPDATE_TIME'                => $now,
                'UL_LEAD_CUSTOMER_LAST_VISITED_TIME' => $now,
                'UL_LEAD_DISPOSITION'                => 'New Lead',
                'UL_LEAD_SUB_DISPOSITION'            => 'Sign Up',
                'UL_LEAD_USER_TYPE'                  => $request->unlisted_user_type,
                'UL_LEAD_COMPANY'                    => '',
                'UL_LEAD_LANDING_PAGE'               => $request->input('landing_page', '/'),
                'UL_LEAD_REQUEST_FOR_CALL'           => 'no',
            ]);

            return $user;
        });

        $request->session()->regenerate();

        session([
            'uid'                => $user->uid,
            'session_token'      => $user->session_token,
            'name'               => $user->name,
            'email'              => $user->email,
            'phone'              => $user->phone,
            'user_type'          => $user->user_type,
            'unlisted_user_type' => $user->unlisted_user_type,
        ]);

        session()->forget(['otp_verified_email', 'otp_verified_until']);

        $returnTo = session()->pull('invest_return_to', '/');

        return response()->json([
            'success'  => true,
            'message'  => 'Account created successfully! Redirecting...',
            'redirect' => url($returnTo),
        ]);
    }

    // ─── Registration email OTP ─────────────────────────────────────────

    public function sendRegistrationOtp(Request $request)
    {
        if ($request->filled('_hp')) {
            return response()->json(['success' => true]);
        }

        try {
            $request->validate([
                'name'  => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
            ], [
                'name.required'  => 'Full name is required.',
                'email.required' => 'Email address is required.',
                'email.email'    => 'Please enter a valid email address.',
                'email.unique'   => 'This email is already registered. Please sign in instead.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $email = strtolower(trim($request->email));
        $code  = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($email, $code, $request) {
            // Invalidate any earlier still-live codes for this email so only
            // the latest one is ever accepted.
            EmailOtp::where('email', $email)
                ->where('purpose', 'registration')
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);

            EmailOtp::create([
                'email'      => $email,
                'purpose'    => 'registration',
                'code_hash'  => Hash::make($code),
                'ip_address' => $request->ip(),
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            ]);
        });

        try {
            Mail::to($email)->send(new EmailOtpMail($code, 'registration'));
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Could not send the OTP email right now. Please try again in a moment.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'A verification code has been sent to your email.',
        ]);
    }

    public function verifyRegistrationOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp'   => 'required|digits:6',
            ], [
                'email.required' => 'Email address is required.',
                'email.email'    => 'Please enter a valid email address.',
                'otp.required'   => 'Please enter the OTP.',
                'otp.digits'     => 'OTP must be a 6-digit code.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $email = strtolower(trim($request->email));

        $otpRow = EmailOtp::where('email', $email)
            ->where('purpose', 'registration')
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$otpRow || $otpRow->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'This code has expired. Please request a new one.',
            ], 422);
        }

        if ($otpRow->attempts >= self::OTP_MAX_ATTEMPTS) {
            $otpRow->update(['consumed_at' => now()]);
            return response()->json([
                'success' => false,
                'message' => 'Too many incorrect attempts. Please request a new code.',
            ], 422);
        }

        if (!Hash::check($request->otp, $otpRow->code_hash)) {
            $otpRow->increment('attempts');
            $remaining = self::OTP_MAX_ATTEMPTS - $otpRow->attempts;

            return response()->json([
                'success' => false,
                'message' => $remaining > 0
                    ? "Incorrect code. {$remaining} attempt(s) left."
                    : 'Too many incorrect attempts. Please request a new code.',
            ], 422);
        }

        $otpRow->update(['consumed_at' => now()]);

        session([
            'otp_verified_email' => $email,
            'otp_verified_until' => now()->addMinutes(self::OTP_VERIFIED_WINDOW),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified.',
        ]);
    }

    // ─── Login email OTP ────────────────────────────────────────────────

    public function sendLoginOtp(Request $request)
    {
        if ($request->filled('_hp')) {
            return response()->json(['success' => true]);
        }

        try {
            $request->validate([
                'email' => 'required|email',
            ], [
                'email.required' => 'Email address is required.',
                'email.email'    => 'Please enter a valid email address.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $email = strtolower(trim($request->email));
        $user  = User::query()->where('email', $email)->first();

        // Always respond the same way whether or not the account exists —
        // this endpoint must never be usable to enumerate registered emails.
        if ($user) {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            DB::transaction(function () use ($email, $code, $request) {
                EmailOtp::where('email', $email)
                    ->where('purpose', 'login')
                    ->whereNull('consumed_at')
                    ->update(['consumed_at' => now()]);

                EmailOtp::create([
                    'email'      => $email,
                    'purpose'    => 'login',
                    'code_hash'  => Hash::make($code),
                    'ip_address' => $request->ip(),
                    'attempts'   => 0,
                    'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                ]);
            });

            try {
                Mail::to($email)->send(new EmailOtpMail($code, 'login'));
            } catch (\Throwable $e) {
                report($e);
                return response()->json([
                    'success' => false,
                    'message' => 'Could not send the OTP email right now. Please try again in a moment.',
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'If this email is registered, a sign-in code has been sent to it.',
        ]);
    }

    public function verifyLoginOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp'   => 'required|digits:6',
            ], [
                'email.required' => 'Email address is required.',
                'email.email'    => 'Please enter a valid email address.',
                'otp.required'   => 'Please enter the OTP.',
                'otp.digits'     => 'OTP must be a 6-digit code.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $email = strtolower(trim($request->email));

        $otpRow = EmailOtp::where('email', $email)
            ->where('purpose', 'login')
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$otpRow || $otpRow->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'This code has expired. Please request a new one.',
            ], 422);
        }

        if ($otpRow->attempts >= self::OTP_MAX_ATTEMPTS) {
            $otpRow->update(['consumed_at' => now()]);
            return response()->json([
                'success' => false,
                'message' => 'Too many incorrect attempts. Please request a new code.',
            ], 422);
        }

        if (!Hash::check($request->otp, $otpRow->code_hash)) {
            $otpRow->increment('attempts');
            $remaining = self::OTP_MAX_ATTEMPTS - $otpRow->attempts;

            return response()->json([
                'success' => false,
                'message' => $remaining > 0
                    ? "Incorrect code. {$remaining} attempt(s) left."
                    : 'Too many incorrect attempts. Please request a new code.',
            ], 422);
        }

        // The OTP row only ever gets created for an email that matched a
        // real user at send-time, so this should always resolve.
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found. Please try again.',
            ], 422);
        }

        $otpRow->update(['consumed_at' => now()]);
        $this->clearFailures($user);

        return $this->logUserIn($request, $user);
    }

    // ─── Login ───────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        // Honeypot
        if ($request->filled('_hp')) {
            return response()->json(['success' => true, 'redirect' => url('/')]);
        }

        $loginType = $request->input('login_type', 'email');

        try {
            if ($loginType === 'phone') {
                $request->validate([
                    'phone'    => 'required|digits:10',
                    'password' => 'required',
                ], [
                    'phone.required'    => 'Phone number is required.',
                    'phone.digits'      => 'Phone number must be exactly 10 digits.',
                    'password.required' => 'Password is required.',
                ]);
            } else {
                $request->validate([
                    'email'    => 'required|email',
                    'password' => 'required',
                ], [
                    'email.required'    => 'Email address is required.',
                    'email.email'       => 'Please enter a valid email address.',
                    'password.required' => 'Password is required.',
                ]);
            }
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        // Single query — fetch user and check lockout on the same row
        $user = $loginType === 'phone'
            ? User::query()->where('phone', $request->phone)->first()
            : User::query()->where('email', $request->email)->first();

        // Unknown identifier — don't leak whether account exists
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials. Please try again.',
            ], 401);
        }

        // Account lockout check (no extra query — data already on $user)
        if ($this->isLocked($user)) {
            $minutesLeft = (int) ceil(now()->diffInSeconds($user->login_locked_until) / 60);
            return response()->json([
                'success' => false,
                'message' => "Account locked. Try again in {$minutesLeft} minute" . ($minutesLeft === 1 ? '' : 's') . '.',
            ], 429);
        }

        // Wrong password
        if (!Hash::check($request->password, $user->password)) {
            $this->recordFailure($user);

            $remaining = max(0, self::MAX_FAILS - $user->failed_login_attempts);
            $message   = 'Invalid credentials. Please try again.';

            if ($remaining <= 2 && $remaining > 0) {
                $message .= " ({$remaining} attempt" . ($remaining === 1 ? '' : 's') . " left before lockout)";
            } elseif ($remaining === 0) {
                $mins    = $this->lockoutMinutes($user);
                $display = $mins >= 60 ? ($mins / 60) . ' hour(s)' : $mins . ' minutes';
                $message = "Too many failed attempts. Account locked for {$display}.";
            }

            return response()->json(['success' => false, 'message' => $message], 401);
        }

        // ✓ Success — clear counters and rotate session token
        $this->clearFailures($user);

        return $this->logUserIn($request, $user);
    }

    private function logUserIn(Request $request, User $user)
    {
        $newToken = Str::random(60);
        $user->update(['session_token' => $newToken]);

        $request->session()->regenerate();

        session([
            'uid'                => $user->uid,
            'session_token'      => $newToken,
            'name'               => $user->name,
            'email'              => $user->email,
            'phone'              => $user->phone,
            'user_type'          => $user->user_type,
            'unlisted_user_type' => $user->unlisted_user_type,
            'privilege'          => $user->privilege ?? [],
        ]);

        $returnTo = session()->pull('invest_return_to', '/');

        return response()->json([
            'success'  => true,
            'message'  => 'Logged in successfully! Redirecting...',
            'redirect' => url($returnTo),
        ]);
    }

    // ─── Logout ──────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        $uid = session('uid');

        if ($uid) {
            User::query()->where('uid', $uid)->update(['session_token' => null]);
        }

        $request->session()->flush();

        return redirect()->route('login');
    }
}
