<?php

namespace App\Http\Controllers;

use App\Models\UnlistedLead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_FAILS = 5;

    // Lockout durations per lockout_count: 1st=15min, 2nd=30min, 3rd=1hr, 4th=6hr, 5th+=24hr
    private const LOCKOUT_SCHEDULE = [15, 30, 60, 360, 1440];

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

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name'               => $request->name,
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

        session([
            'uid'                => $user->uid,
            'session_token'      => $user->session_token,
            'name'               => $user->name,
            'email'              => $user->email,
            'phone'              => $user->phone,
            'user_type'          => $user->user_type,
            'unlisted_user_type' => $user->unlisted_user_type,
        ]);

        $returnTo = session()->pull('invest_return_to', '/');

        return response()->json([
            'success'  => true,
            'message'  => 'Account created successfully! Redirecting...',
            'redirect' => url($returnTo),
        ]);
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
            ? User::where('phone', $request->phone)->first()
            : User::where('email', $request->email)->first();

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

        $newToken = Str::random(60);
        $user->update(['session_token' => $newToken]);

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
            User::where('uid', $uid)->update(['session_token' => null]);
        }

        $request->session()->flush();

        return redirect()->route('login');
    }
}
