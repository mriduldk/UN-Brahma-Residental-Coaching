<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('Middleware: EnsureOrganizationExists hit for ' . $request->url());
        $user = auth()->user();

        if ($user && $user->role === 'administration') {
            // Check if user belongs to ANY organization (owned or member)
            $hasOrganization = $user->organizations()->exists();

            if (!$hasOrganization) {
                // If organization doesn't exist, redirect to onboarding
                if (!$request->routeIs('administration.onboarding') && !$request->routeIs('administration.onboarding.store')) {
                    return redirect()->route('administration.onboarding');
                }
            } else {
                // If organization exists but they try to access onboarding, redirect to dashboard
                if ($request->routeIs('administration.onboarding')) {
                    return redirect()->route('administration.dashboard');
                }
            }
        }

        \Log::info('Middleware: EnsureOrganizationExists passing request for ' . $request->url());
        return $next($request);
    }
}
