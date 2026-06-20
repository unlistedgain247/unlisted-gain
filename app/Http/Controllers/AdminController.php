<?php

namespace App\Http\Controllers;

use App\Helpers\Privilege;
use App\Models\User;

class AdminController extends Controller
{
    public function redirectToDashboard()
    {
        if (!empty(Privilege::get('admin'))) {
            return redirect()->route('admin.dashboard');
        }
        if (!empty(Privilege::get('user_master'))) {
            return redirect()->route('admin.users');
        }
        if (!empty(Privilege::get('unlisted.stockx'))) {
            return redirect()->route('admin.unlisted');
        }
        if (!empty(Privilege::get('unlisted.leads')) || !empty(Privilege::get('unlisted.leads_allocation'))) {
            return redirect()->route('admin.unlisted.leads');
        }
        if (!empty(Privilege::get('pg.margin'))) {
            return redirect()->route('admin.pg.margin');
        }
        if (!empty(Privilege::get('pg.margin_error'))) {
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
