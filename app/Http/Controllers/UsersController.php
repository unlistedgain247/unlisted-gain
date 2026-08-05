<?php

namespace App\Http\Controllers;

use App\Helpers\Privilege;
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
        $user = User::query()->where('uid', $uid)->firstOrFail();

        return view('admin.users.kyc-docs-modal', compact('user'));
    }

    public function verifyKyc(Request $request, string $uid, string $type)
    {
        $user   = User::query()->where('uid', $uid)->firstOrFail();
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
        $user = User::query()->where('uid', $uid)->firstOrFail();

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
        User::query()->where('uid', $uid)->firstOrFail()->update([
            'failed_login_attempts' => 0,
            'login_locked_until'    => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Lockout cleared.']);
    }

    public function getPrivilegeModal(string $uid)
    {
        $user = User::query()->where('uid', $uid)->firstOrFail();
        $canGrantAdmin = (bool) Privilege::get('admin');

        return view('admin.partials.privilege-modal', compact('user', 'canGrantAdmin'));
    }

    public function savePrivilege(Request $request, string $uid)
    {
        $user = User::query()->where('uid', $uid)->firstOrFail();

        // A caller may only grant/revoke a privilege they already hold themselves
        // (or hold admin) — otherwise the target user's existing value for that
        // key is left untouched. This closes self-escalation (a user_master-only
        // account could previously grant itself pg/unlisted/cms privileges it
        // doesn't have) and a related bug where a form that doesn't render
        // fields for privileges the caller can't see would silently wipe them
        // (missing checkbox -> $request->boolean() defaults to false).
        $isAdmin    = (bool) Privilege::get('admin');
        $callerPriv = Privilege::get() ?: [];
        $existing   = $user->privilege ?? [];

        $grant = function (string $key, string $field) use ($isAdmin, $callerPriv, $existing, $request) {
            if ($isAdmin || data_get($callerPriv, $key)) {
                return $request->boolean($field);
            }
            return (bool) data_get($existing, $key);
        };

        $privilege = [
            'admin'       => $grant('admin', 'admin'),
            'user_master' => $grant('user_master', 'user_master'),
            'unlisted'    => [
                'stocks'           => $grant('unlisted.stocks', 'unlisted_stocks'),
                'leads'            => $grant('unlisted.leads', 'unlisted_leads'),
                'leads_allocation' => $grant('unlisted.leads_allocation', 'unlisted_leads_allocation'),
                'orders'           => $grant('unlisted.orders', 'unlisted_orders'),
                'news'             => $grant('unlisted.news', 'unlisted_news'),
                'unlisted_reports' => $grant('unlisted.unlisted_reports', 'unlisted_unlisted_reports'),
                'order_backend'    => $grant('unlisted.order_backend', 'unlisted_order_backend'),
            ],
            'pg' => [
                'dashboard'    => $grant('pg.dashboard', 'pg_dashboard'),
                'margin'       => $grant('pg.margin', 'pg_margin'),
                'margin_error' => $grant('pg.margin_error', 'pg_margin_error'),
                'transactions' => $grant('pg.transactions', 'pg_transactions'),
            ],
            'cms' => [
                'author'   => $grant('cms.author', 'cms_author'),
                'reviewer' => $grant('cms.reviewer', 'cms_reviewer'),
            ],
        ];

        $user->update(['privilege' => $privilege]);

        if (session('uid') == $uid) {
            session(['privilege' => $privilege]);
        }

        return response()->json(['success' => true, 'message' => 'Privileges saved.']);
    }
}
