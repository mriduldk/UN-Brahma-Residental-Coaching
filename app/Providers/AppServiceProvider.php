<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enforce application timezone
        date_default_timezone_set(config('app.timezone', 'Asia/Kolkata'));

        // Restrict Log Viewer to admin users only in production
        LogViewer::auth(function (Request $request) {
            // In local/dev: allow all authenticated users
            if (app()->environment('local')) {
                return auth()->check();
            }
            // In production: only allow users with the 'admin' role
            return auth()->check() && auth()->user()->role === 'admin';
        });

        // Payment Processing: 5 attempts per minute per user
        RateLimiter::for('payment', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return back()->with('error', 'Too many payment requests. Please wait a moment and try again.');
                });
        });

        // Application Submission: 3 per minute per user
        RateLimiter::for('application-submission', function (Request $request) {
            return Limit::perMinute(3)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return back()->with('error', 'Too many submission attempts. Please try again shortly.');
                });
        });

        // File Uploads: 15 per minute per user
        RateLimiter::for('file-upload', function (Request $request) {
            return Limit::perMinute(15)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return back()->with('error', 'Too many file uploads. Please wait a minute before uploading again.');
                });
        });

        // Profile Update: 10 per minute per user
        RateLimiter::for('profile-update', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return back()->with('error', 'Too many update requests. Please slow down.');
                });
        });

        // Contact / Public forms: 5 per minute per IP
        RateLimiter::for('public-form', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())
                ->response(function () {
                    return back()->with('error', 'You are submitting too quickly. Please wait a moment.');
                });
        });
    }
}
