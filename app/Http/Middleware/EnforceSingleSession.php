<?php

namespace App\Http\Middleware;

use App\Helpers\SessionAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceSingleSession
{
    /**
     * SessionAuth::valid() (session_token vs the DB copy, rotated on every
     * login) already exists and correctly detects "logged in on another
     * device" — but until now it was only checked in the admin panel, the
     * profile page, and one leads route. Every other page — including the
     * header's logged-in state on the public site — read session('uid')
     * directly with no validity check at all, so a superseded device kept
     * showing as logged in indefinitely everywhere else. Running this
     * globally makes the existing single-session mechanism actually apply
     * site-wide instead of only inside a few gated routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('uid') && !SessionAuth::valid()) {
            $request->session()->flush();
        }

        return $next($request);
    }
}
