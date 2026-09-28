<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'institute/admissions/*/payment/callback',
            'administration/admissions/*/payment/callback',
            'student/payment/process',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'ensure_student_profile' => \App\Http\Middleware\EnsureStudentProfileExists::class,
            'mobile.verified' => \App\Http\Middleware\EnsureMobileVerified::class,
            'enforce.subscription' => \App\Http\Middleware\EnforceActiveSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
