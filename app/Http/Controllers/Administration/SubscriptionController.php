<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use App\Models\SubscriptionPayment;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index(SubscriptionService $service)
    {
        $organization = auth()->user()->currentOrganization();
        $subscription = $service->getSubscriptionFor($organization);
        $amountDue = $service->calculateAmountDue($subscription);
        $isBlocked = $service->isBlocked($organization);
        $isPaymentDue = $subscription ? now()->greaterThan($subscription->current_period_end) : false;

        $payments = $organization->subscriptionPayments()->latest()->get();

        return view('administration.subscription.billing', compact('subscription', 'amountDue', 'isBlocked', 'isPaymentDue', 'organization', 'payments'));
    }

    public function refreshPaymentStatus(Request $request, $orderId, SubscriptionService $service)
    {
        $organization = auth()->user()->currentOrganization();
        
        $payment = SubscriptionPayment::where('razorpay_order_id', $orderId)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        if ($payment->status === 'successful') {
            return redirect()->route('administration.subscription.billing')->with('success', 'Payment is already marked as successful.');
        }

        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            
            // Fetch payments for this order
            $payments = $api->order->fetch($orderId)->payments();

            $successfulPayment = null;
            if ($payments && isset($payments['items'])) {
                foreach ($payments['items'] as $rzpPayment) {
                    if ($rzpPayment['status'] === 'captured') {
                        $successfulPayment = $rzpPayment;
                        break;
                    }
                }
            }

            if ($successfulPayment) {
                $paymentMode = $successfulPayment['method'] ?? null;
                $paymentDetails = is_object($successfulPayment) && method_exists($successfulPayment, 'toArray') 
                    ? $successfulPayment->toArray() 
                    : (array)$successfulPayment;

                // Update payment record
                $payment->update([
                    'razorpay_payment_id' => $successfulPayment['id'],
                    'status' => 'successful',
                    'paid_at' => now(),
                    'payment_mode' => $paymentMode,
                    'payment_details' => $paymentDetails,
                ]);

                // Update subscription
                $subscription = $payment->subscription;
                if ($subscription->status !== 'active' || now()->greaterThan($subscription->current_period_end)) {
                    $subscription->update([
                        'status' => 'active',
                        'current_period_start' => now(),
                        'current_period_end' => now()->addMonth(),
                    ]);
                }

                return redirect()->route('administration.subscription.billing')->with('success', 'Payment status updated to successful!');
            }

            // If not captured, mark as failed if it was pending
            if ($payment->status === 'pending') {
                $payment->update(['status' => 'failed']);
            }

            return redirect()->route('administration.subscription.billing')->with('error', 'No successful payment found for this order.');

        } catch (\Exception $e) {
            Log::error('Razorpay Refresh Payment Error: ' . $e->getMessage());
            return redirect()->route('administration.subscription.billing')->with('error', 'Failed to refresh payment status. Please try again.');
        }
    }

    public function createOrder(Request $request, SubscriptionService $service)
    {
        $organization = auth()->user()->currentOrganization();
        $subscription = $service->getSubscriptionFor($organization);
        $amountDue = $service->calculateAmountDue($subscription);

        if ($amountDue <= 0) {
            return response()->json(['error' => 'No payment due at this time.'], 400);
        }

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            $order = $api->order->create([
                'receipt' => 'sub_rcptid_' . $subscription->id . '_' . time(),
                'amount' => $amountDue * 100, // in paise
                'currency' => 'INR'
            ]);

            // Record pending payment
            SubscriptionPayment::create([
                'organization_id' => $organization->id,
                'organization_subscription_id' => $subscription->id,
                'razorpay_order_id' => $order['id'],
                'amount' => $amountDue,
                'status' => 'pending',
            ]);

            return response()->json([
                'order_id' => $order['id'],
                'amount' => $amountDue * 100,
                'key' => env('RAZORPAY_KEY'),
                'organization_name' => $organization->organization_name,
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay Subscription Order Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to initiate payment. Please try again later.'], 500);
        }
    }

    public function verifyPayment(Request $request, SubscriptionService $service)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string'
        ]);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            $attributes = array(
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            );

            $api->utility->verifyPaymentSignature($attributes);

            // Fetch payment details to get method
            $rzpPayment = $api->payment->fetch($request->razorpay_payment_id);
            $paymentMode = $rzpPayment['method'] ?? null;
            
            // Convert to array properly whether it's an object or array
            $paymentDetails = is_object($rzpPayment) && method_exists($rzpPayment, 'toArray') 
                ? $rzpPayment->toArray() 
                : (array)$rzpPayment;

            // Update payment record
            $payment = SubscriptionPayment::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();
            $payment->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'status' => 'successful',
                'paid_at' => now(),
                'payment_mode' => $paymentMode,
                'payment_details' => $paymentDetails,
            ]);

            // Update subscription
            $subscription = $payment->subscription;
            $subscription->update([
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(), // Extend by 1 month
            ]);

            return redirect()->route('administration.subscription.billing')->with('success', 'Payment successful! Your subscription has been renewed.');

        } catch (\Exception $e) {
            Log::error('Razorpay Subscription Verification Error: ' . $e->getMessage());
            
            // Mark payment as failed
            $payment = SubscriptionPayment::where('razorpay_order_id', $request->razorpay_order_id)->first();
            if ($payment) {
                $payment->update(['status' => 'failed']);
            }

            return redirect()->route('administration.subscription.billing')->with('error', 'Payment verification failed. If money was deducted, it will be refunded automatically.');
        }
    }
}
