<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function redirectToDashboard()
    {
        if (!empty(user_privilege('admin'))) {
            return redirect()->route('admin.dashboard');
        }
        if (!empty(user_privilege('user_master'))) {
            return redirect()->route('admin.users');
        }
        if (!empty(user_privilege('unlisted.stockx'))) {
            return redirect()->route('admin.unlisted');
        }
        if (!empty(user_privilege('unlisted.leads')) || !empty(user_privilege('unlisted.leads_allocation'))) {
            return redirect()->route('admin.unlisted.leads');
        }
        if (!empty(user_privilege('pg.margin'))) {
            return redirect()->route('admin.pg.margin');
        }
        if (!empty(user_privilege('pg.margin_error'))) {
            return redirect()->route('admin.pg.margin-error');
        }

        abort(403, 'You do not have admin access.');
    }

    public function dashboard()
    {
        $totalUsers  = User::count();
        $adminUsers  = User::whereNotNull('privilege')->get()->filter(fn($u) => !empty($u->privilege['admin']))->count();
        $memberUsers = User::where('user_type', 'member')->count();

        return view('admin.dashboard', compact('totalUsers', 'adminUsers', 'memberUsers'));
    }
}
