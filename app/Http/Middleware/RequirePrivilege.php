<?php

namespace App\Http\Middleware;

use App\Helpers\Privilege;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePrivilege
{
    /**
     * Accepts one or more privilege keys (comma-separated).
     * Access is granted if the user has ANY of them.
     *
     * Special key 'unlisted' → passes if ANY unlisted sub-privilege is true.
     * Special key 'pg' → passes if ANY pg sub-privilege is true.
     * Dotted keys (e.g. 'pg.dashboard') check that specific sub-privilege.
     *
     * Usage:
     *   middleware('privilege:admin')
     *   middleware('privilege:admin,user_master')
     *   middleware('privilege:unlisted')
     *   middleware('privilege:pg.dashboard')
     */
    public function handle(Request $request, Closure $next, string ...$keys): Response
    {
        if (!session('uid')) {
            return redirect()->route('login');
        }

        $privilege = Privilege::get();

        foreach ($keys as $key) {
            if ($key === 'unlisted') {
                if (!empty(array_filter($privilege['unlisted'] ?? []))) {
                    return $next($request);
                }
            } elseif ($key === 'pg') {
                if (!empty(array_filter($privilege['pg'] ?? []))) {
                    return $next($request);
                }
            } elseif (str_contains($key, '.')) {
                if (!empty(data_get($privilege, $key))) {
                    return $next($request);
                }
            } elseif (!empty($privilege[$key])) {
                return $next($request);
            }
        }

        abort(403, 'You do not have access to this section.');
    }
}
