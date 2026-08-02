<?php

namespace App\Http\Middleware;

use App\Helpers\SessionAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (SessionAuth::valid()) {
            return redirect('/');
        }

        return $next($request);
    }
}
