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

        // OTP send: 3/10min + 5/hour per email, 10/hour per IP.
        // Email is the primary key here — it's what an attacker can't rotate
        // as cheaply as an IP, so it has to be the layer that actually holds.
        RateLimiter::for('otp-send', function (Request $request) {
            $email = strtolower(trim($request->input('email', '')));

            return [
                Limit::perMinutes(10, 3)
                    ->by('otp-send|email:' . $email)
                    ->response(fn () => response()->json([
                        'success' => false,
                        'message' => 'Too many OTP requests for this email. Please wait a few minutes.',
                    ], 429)),

                Limit::perHour(5)
                    ->by('otp-send|email:' . $email)
                    ->response(fn () => response()->json([
                        'success' => false,
                        'message' => 'Too many OTP requests for this email. Please try again later.',
                    ], 429)),

                Limit::perHour(10)
                    ->by('otp-send|ip:' . $request->ip())
                    ->response(fn () => response()->json([
                        'success' => false,
                        'message' => 'Too many requests from your network. Please try again later.',
                    ], 429)),
            ];
        });

        // OTP verify: 8/10min per email (on top of the per-code attempt cap
        // stored in registration_otps.attempts), 20/hour per IP.
        RateLimiter::for('otp-verify', function (Request $request) {
            $email = strtolower(trim($request->input('email', '')));

            return [
                Limit::perMinutes(10, 8)
                    ->by('otp-verify|email:' . $email)
                    ->response(fn () => response()->json([
                        'success' => false,
                        'message' => 'Too many attempts. Please wait a few minutes and request a new code.',
                    ], 429)),

                Limit::perHour(20)
                    ->by('otp-verify|ip:' . $request->ip())
                    ->response(fn () => response()->json([
                        'success' => false,
                        'message' => 'Too many attempts from your network. Please try again later.',
                    ], 429)),
            ];
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
