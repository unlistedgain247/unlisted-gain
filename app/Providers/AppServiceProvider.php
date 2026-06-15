<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Login: 5 attempts/min per IP+identifier, 15/min per IP
        RateLimiter::for('login', function (Request $request) {
            $identifier = strtolower(trim(
                $request->input('login_type') === 'phone'
                    ? $request->input('phone', '')
                    : $request->input('email', '')
            ));

            return [
                Limit::perMinute(5)
                    ->by('login|id:' . $identifier . '|ip:' . $request->ip())
                    ->response(fn () => response()->json([
                        'success' => false,
                        'message' => 'Too many login attempts for this account. Please wait a minute.',
                    ], 429)),

                Limit::perMinute(15)
                    ->by('login|ip:' . $request->ip())
                    ->response(fn () => response()->json([
                        'success' => false,
                        'message' => 'Too many requests from your IP. Please wait a minute.',
                    ], 429)),
            ];
        });

        // Register: 5 registrations/hour per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(5)
                ->by('register|ip:' . $request->ip())
                ->response(fn () => response()->json([
                    'success' => false,
                    'message' => 'Too many registration attempts. Please try again later.',
                ], 429));
        });
    }
}
