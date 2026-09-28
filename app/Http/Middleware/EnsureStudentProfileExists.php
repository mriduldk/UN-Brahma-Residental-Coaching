<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentProfileExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->role === 'student') {
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                // If student profile doesn't exist, redirect to onboarding
                // But avoid redirect loop if already on onboarding route
                if (!$request->routeIs('student.onboarding') && !$request->routeIs('student.onboarding.store')) {
                    return redirect()->route('student.onboarding');
                }
            } else {
                // If student exists but they try to access onboarding, redirect to dashboard
                if ($request->routeIs('student.onboarding')) {
                    return redirect()->route('student.dashboard');
                }
            }
        }

        return $next($request);
    }
}
