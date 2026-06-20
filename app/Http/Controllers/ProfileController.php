<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    private function authUser(): ?User
    {
        if (!session('uid') || !session('session_token')) {
            return null;
        }
        return User::whereKey(session('uid'))->first();
    }

    public function show()
    {
        $user = $this->authUser();
        if (!$user) {
            return redirect()->route('login');
        }
        return view('public.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $this->authUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Session expired.'], 401);
        }

        try {
            $request->validate([
                'name'  => 'required|string|max:100',
                'phone' => "required|digits:10|unique:users,phone,{$user->uid},uid",
            ], [
                'name.required'   => 'Full name is required.',
                'phone.required'  => 'Phone number is required.',
                'phone.digits'    => 'Phone number must be exactly 10 digits.',
                'phone.unique'    => 'This phone number is already in use.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        session(['name' => $user->name, 'phone' => $user->phone]);

        return response()->json(['success' => true, 'message' => 'Profile updated successfully.']);
    }

    public function updatePassword(Request $request)
    {
        $user = $this->authUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Session expired.'], 401);
        }

        try {
            $request->validate([
                'new_password' => 'required|min:6|confirmed',
            ], [
                'new_password.required'  => 'New password is required.',
                'new_password.min'       => 'Password must be at least 6 characters.',
                'new_password.confirmed' => 'Password confirmation does not match.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $user->update(['password' => $request->new_password]);

        return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
    }

    // ── KYC: Bank ────────────────────────────────────────────

    public function uploadBank(Request $request)
    {
        $user = $this->authUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Session expired.'], 401);
        }

        try {
            $request->validate([
                'bank_holder_name' => 'required|string|max:100',
                'bank_name'        => 'required|string|max:100',
                'bank_account_no'  => 'required|string|max:30',
                'bank_ifsc_code'   => 'required|string|max:15',
                'bank_cancelled_check' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ], [
                'bank_holder_name.required' => 'Account holder name is required.',
                'bank_name.required'        => 'Bank name is required.',
                'bank_account_no.required'  => 'Account number is required.',
                'bank_ifsc_code.required'   => 'IFSC code is required.',
                'bank_cancelled_check.mimes' => 'File must be JPG, PNG, or PDF.',
                'bank_cancelled_check.max'   => 'File size must not exceed 5 MB.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = [
            'bank_holder_name' => $request->bank_holder_name,
            'bank_name'        => $request->bank_name,
            'bank_account_no'  => $request->bank_account_no,
            'bank_ifsc_code'   => strtoupper($request->bank_ifsc_code),
            'bank_verified'    => 0,
        ];

        if ($request->hasFile('bank_cancelled_check')) {
            $path = $this->storeKycFile($request->file('bank_cancelled_check'), 'bank-check', $user->uid);
            $data['bank_cancelled_check'] = $path;
        }

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'Bank details saved. Pending verification.']);
    }

    // ── KYC: Demat ───────────────────────────────────────────

    public function uploadDemat(Request $request)
    {
        $user = $this->authUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Session expired.'], 401);
        }

        try {
            $request->validate([
                'demat_dp_id'   => 'required|string|min:16|max:50|regex:/^[A-Za-z0-9]+$/',
                'demat_dp_name' => 'required|string|max:100',
                'demat_cml_copy' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ], [
                'demat_dp_id.required'   => 'DP ID is required.',
                'demat_dp_name.required' => 'DP Name is required.',
                'demat_cml_copy.mimes'   => 'File must be JPG, PNG, or PDF.',
                'demat_cml_copy.max'     => 'File size must not exceed 5 MB.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = [
            'demat_dp_id'    => $request->demat_dp_id,
            'demat_dp_name'  => $request->demat_dp_name,
            'demat_verified' => 0,
        ];

        if ($request->hasFile('demat_cml_copy')) {
            $path = $this->storeKycFile($request->file('demat_cml_copy'), 'cml-copy', $user->uid);
            $data['demat_cml_copy'] = $path;
        }

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'Demat details saved. Pending verification.']);
    }

    // ── KYC: PAN ─────────────────────────────────────────────

    public function uploadPan(Request $request)
    {
        $user = $this->authUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Session expired.'], 401);
        }

        try {
            $request->validate([
                'user_pan_no'    => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
                'user_pan_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ], [
                'user_pan_no.required' => 'PAN number is required.',
                'user_pan_no.size'     => 'PAN number must be exactly 10 characters.',
                'user_pan_no.regex'    => 'Invalid PAN format. Example: ABCDE1234F',
                'user_pan_image.mimes' => 'File must be JPG, PNG, or PDF.',
                'user_pan_image.max'   => 'File size must not exceed 5 MB.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = [
            'user_pan_no'       => strtoupper($request->user_pan_no),
            'user_pan_verified' => 0,
        ];

        if ($request->hasFile('user_pan_image')) {
            $path = $this->storeKycFile($request->file('user_pan_image'), 'pan-copy', $user->uid);
            $data['user_pan_image'] = $path;
        }

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'PAN details saved. Pending verification.']);
    }

    // ── Avatar ───────────────────────────────────────────────

    public function uploadAvatar(Request $request)
    {
        $user = $this->authUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Session expired.'], 401);
        }

        try {
            $request->validate([
                'avatar' => 'required|file|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'avatar.required' => 'Please select an image.',
                'avatar.mimes'    => 'Image must be JPG, PNG, or WebP.',
                'avatar.max'      => 'Image must not exceed 2 MB.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $file = $request->file('avatar');
        $ext  = strtolower($file->getClientOriginalExtension());
        $dir  = 'private/avatars';

        Storage::makeDirectory($dir);

        // Delete any old avatar for this user
        foreach (Storage::files($dir) as $existing) {
            if (str_starts_with(basename($existing), 'avatar-' . $user->uid . '.')) {
                Storage::delete($existing);
            }
        }

        $filename = 'avatar-' . $user->uid . '.' . $ext;
        $file->storeAs($dir, $filename);
        $path = $dir . '/' . $filename;

        $user->update(['avatar_path' => $path]);

        return response()->json(['success' => true, 'message' => 'Profile photo updated.']);
    }

    public function serveAvatar(int $uid)
    {
        $user = User::whereKey($uid)->first();
        if (!$user || !$user->avatar_path || !Storage::exists($user->avatar_path)) {
            abort(404);
        }

        $mime = Storage::mimeType($user->avatar_path) ?: 'image/jpeg';

        return response()->file(Storage::path($user->avatar_path), [
            'Content-Type'           => $mime,
            'Cache-Control'          => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    // ── File helpers ──────────────────────────────────────────

    private function storeKycFile(UploadedFile $file, string $folder, int|string $uid): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $dir = 'private/kyc/' . $folder;

        $prefix = match ($folder) {
            'bank-check' => 'cancel-check-',
            'pan-copy'   => 'pan-copy-',
            'cml-copy'   => 'cml-copy-',
            default      => $folder . '-',
        };

        // Delete any existing file for this user in this folder (any extension)
        foreach (Storage::files($dir) as $existing) {
            if (str_starts_with(basename($existing), $prefix . $uid . '.')) {
                Storage::delete($existing);
            }
        }

        $filename = $prefix . $uid . '.' . $ext;
        $file->storeAs($dir, $filename);
        return $dir . '/' . $filename;
    }
}
