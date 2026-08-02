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

        // Password change, avatar/KYC document uploads: 10/min per session+IP.
        // (The global ThrottleRequestsException handler in bootstrap/app.php
        // already renders a clean JSON message, so no custom response here.)
        RateLimiter::for('profile-sensitive', function (Request $request) {
            $key = 'profile-sensitive|uid:' . (session('uid') ?: 'guest') . '|ip:' . $request->ip();
            return Limit::perMinute(10)->by($key);
        });

        // Public search/data endpoints: 60/min per IP — generous enough for
        // normal pagination/search use, enough to blunt scraping/DoS abuse.
        RateLimiter::for('public-data', function (Request $request) {
            return Limit::perMinute(60)->by('public-data|ip:' . $request->ip());
        });
    }
}
