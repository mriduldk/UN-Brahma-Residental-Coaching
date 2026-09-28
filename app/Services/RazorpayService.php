<?php

namespace App\Services;

use Razorpay\Api\Api;
use App\Models\Institution;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    /**
     * Get Razorpay API instance for a specific institution
     */
    public function getApi(Institution $institution)
    {
        $key = $institution->razorpay_key;
        $secret = $institution->razorpay_secret;

        if (!$key || !$secret) {
            throw new \Exception("Razorpay credentials not configured for this institution.");
        }

        return new Api($key, $secret);
    }

    /**
     * Create an order in Razorpay
     */
    public function createOrder(Institution $institution, $amount, $receipt, $notes = [])
    {
        try {
            $api = $this->getApi($institution);

            $orderData = [
                'receipt'         => (string)$receipt,
                'amount'          => $amount * 100, // Razorpay takes amount in paise
                'currency'        => 'INR',
                'notes'           => $notes
            ];

            $order = $api->order->create($orderData);

            return [
                'success' => true,
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
                'json_response' => json_encode($order->toArray())
            ];

        } catch (\Exception $e) {
            Log::error("Razorpay Order Creation Failed: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify Payment Signature
     */
    public function verifySignature(Institution $institution, $attributes)
    {
        try {
            $api = $this->getApi($institution);
            
            // This will throw signature verify exception if verification fails
            $api->utility->verifyPaymentSignature($attributes);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Razorpay Signature Verification Failed: " . $e->getMessage());
            return false;
        }
    }
}
