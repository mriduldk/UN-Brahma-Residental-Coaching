<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\OrganizationSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Carbon;

class SubscriptionService
{
    /**
     * Get or create a subscription for the organization
     */
    public function getSubscriptionFor(Organization $organization)
    {
        $subscription = OrganizationSubscription::where('organization_id', $organization->id)->first();

        if (!$subscription) {
            // Find the corresponding plan based on business_type
            $plan = SubscriptionPlan::where('business_type', $organization->business_type)->first();

            // Default to 'School' if no exact match (or we could throw an exception)
            if (!$plan) {
                $plan = SubscriptionPlan::where('business_type', 'School')->first();
            }

            if ($plan) {
                $trialEndsAt = $organization->created_at->addMonths($plan->free_months);
                
                $subscription = OrganizationSubscription::create([
                    'organization_id' => $organization->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => now()->lessThanOrEqualTo($trialEndsAt) ? 'trialing' : 'past_due',
                    'trial_ends_at' => $trialEndsAt,
                    'current_period_start' => $trialEndsAt,
                    'current_period_end' => $trialEndsAt, // Payment is due when trial ends
                ]);
            }
        }

        return $subscription;
    }

    /**
     * Calculate the current amount due for the organization
     */
    public function calculateAmountDue(OrganizationSubscription $subscription)
    {
        // If there's a custom override, use it
        if (!is_null($subscription->custom_price_per_month)) {
            return $subscription->custom_price_per_month;
        }

        $plan = $subscription->plan;
        
        // Months elapsed since trial ended
        if (now()->lessThanOrEqualTo($subscription->trial_ends_at)) {
            return 0; // Still in free trial
        }

        $monthsSinceTrialEnd = $subscription->trial_ends_at->diffInMonths(now());

        // Check if we are in the discounted period (e.g. next 3 months after trial)
        if ($monthsSinceTrialEnd < $plan->discounted_months && !is_null($plan->discounted_price)) {
            return $plan->discounted_price;
        }

        // Standard rate
        return $plan->standard_price;
    }

    /**
     * Determine if an organization is currently blocked from using features
     */
    public function isBlocked(Organization $organization)
    {
        $subscription = $this->getSubscriptionFor($organization);
        
        if (!$subscription) {
            return false; // Fail safe
        }

        if ($subscription->status === 'trialing' && now()->lessThanOrEqualTo($subscription->trial_ends_at)) {
            return false;
        }

        if ($subscription->status === 'active') {
            // If it's active but the period end has passed + 7 days grace
            if (now()->greaterThan($subscription->current_period_end->copy()->addDays(7))) {
                // Should change status to past_due or suspended, but for now just return blocked
                return true;
            }
            return false;
        }

        // Past due / Suspended / Cancelled
        if (now()->greaterThan($subscription->current_period_end->copy()->addDays(7))) {
            return true;
        }

        return false;
    }
}
