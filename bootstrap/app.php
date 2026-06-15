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
