<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'guest.only' => \App\Http\Middleware\GuestOnly::class,
            'privilege'  => \App\Http\Middleware\RequirePrivilege::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\EnforceSingleSession::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Opt-in only — unset by default, which trusts no proxy (the safe
        // framework default). Only set TRUSTED_PROXIES in .env after confirming
        // with your host that traffic actually passes through a TLS-terminating
        // proxy/load balancer in front of PHP; otherwise trusting proxy headers
        // here lets any client spoof X-Forwarded-For/X-Forwarded-Proto directly
        // (fake source IP for rate limiting, fake HTTPS status for cookies).
        if ($trustedProxies = env('TRUSTED_PROXIES')) {
            $middleware->trustProxies(
                at: $trustedProxies === '*' ? '*' : explode(',', $trustedProxies),
            );
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return JSON for throttle errors so AJAX login/register forms handle them properly
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
            return response()->json([
                'success' => false,
                'message' => "Too many attempts. Please try again in {$retryAfter} seconds.",
            ], 429);
        });

        // Catch DB connection failures — show a clean 503 instead of raw SQL errors
        $exceptions->render(function (QueryException $e, Request $request) {
            $msg = $e->getMessage();
            $isConnErr = str_contains($msg, '[2002]')
                || str_contains($msg, '[2003]')
                || str_contains($msg, 'No connection')
                || str_contains($msg, 'Connection refused')
                || str_contains($msg, 'Connection timed out');

            if (!$isConnErr) return null;

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service temporarily unavailable. Please try again later.',
                ], 503);
            }

            return response()->view('errors.db-down', [], 503);
        });
    })->create();
