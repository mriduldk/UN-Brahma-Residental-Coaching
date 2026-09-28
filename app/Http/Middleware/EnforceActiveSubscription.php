<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user) {
            $organization = $user->currentOrganization();
            
            if ($organization) {
                $subscriptionService = app(\App\Services\SubscriptionService::class);
                
                // Allow super admins or ignore for specific paths if needed
                if (!$user->isSuperAdmin() && $subscriptionService->isBlocked($organization)) {
                    // Check if current route is billing or logout
                    if (!$request->routeIs('administration.subscription.billing') && 
                        !$request->routeIs('administration.subscription.*') &&
                        !$request->routeIs('logout')) {
                        return redirect()->route('administration.subscription.billing')
                            ->with('error', 'Your subscription is past due. Please complete the payment to continue using the platform.');
                    }
                }
            }
        }

        return $next($request);
    }
}
