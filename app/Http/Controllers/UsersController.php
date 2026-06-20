<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('created_at')->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function getKycDocsModal(string $uid)
    {
        $user = User::where('uid', $uid)->firstOrFail();

        return view('admin.users.kyc-docs-modal', compact('user'));
    }

    public function verifyKyc(Request $request, string $uid, string $type)
    {
        $user   = User::where('uid', $uid)->firstOrFail();
        $newVal = $request->input('verified', 1) ? 1 : 0;

        $column = match ($type) {
            'bank'  => 'bank_verified',
            'demat' => 'demat_verified',
            'pan'   => 'user_pan_verified',
            default => null,
        };

        if (!$column) abort(422, 'Invalid KYC type.');

        $user->update([$column => $newVal]);
        $user->refresh();

        $allVerified = $user->bank_verified && $user->demat_verified && $user->user_pan_verified;

        return response()->json(['success' => true, 'all_verified' => $allVerified]);
    }

    public function serveKycFile(string $uid, string $type)
    {
        $user = User::where('uid', $uid)->firstOrFail();

        $path = match ($type) {
            'bank'  => $user->bank_cancelled_check,
            'demat' => $user->demat_cml_copy,
            'pan'   => $user->user_pan_image,
            default => null,
        };

        if (!$path || !Storage::exists($path)) abort(404, 'File not found.');

        $mime = Storage::mimeType($path) ?: 'application/octet-stream';

        return response()->file(Storage::path($path), [
            'Content-Type'           => $mime,
            'Content-Disposition'    => 'inline; filename="' . basename($path) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'no-store',
        ]);
    }

    public function resetLockout(string $uid)
    {
        User::where('uid', $uid)->firstOrFail()->update([
            'failed_login_attempts' => 0,
            'login_locked_until'    => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Lockout cleared.']);
    }

    public function getPrivilegeModal(string $uid)
    {
        $user = User::where('uid', $uid)->firstOrFail();

        return view('admin.partials.privilege-modal', compact('user'));
    }

    public function savePrivilege(Request $request, string $uid)
    {
        $user = User::where('uid', $uid)->firstOrFail();

        $privilege = [
            'admin'       => $request->boolean('admin'),
            'user_master' => $request->boolean('user_master'),
            'unlisted'    => [
                'stockx'           => $request->boolean('unlisted_stockx'),
                'leads'            => $request->boolean('unlisted_leads'),
                'leads_allocation' => $request->boolean('unlisted_leads_allocation'),
                'orders'           => $request->boolean('unlisted_orders'),
                'unlisted_reports' => $request->boolean('unlisted_unlisted_reports'),
                'bidding_backend'  => $request->boolean('unlisted_bidding_backend'),
                'order_backend'    => $request->boolean('unlisted_order_backend'),
            ],
            'pg' => [
                'margin'       => $request->boolean('pg_margin'),
                'margin_error' => $request->boolean('pg_margin_error'),
            ],
        ];

        $user->update(['privilege' => $privilege]);

        return response()->json(['success' => true, 'message' => 'Privileges saved.']);
    }
}
