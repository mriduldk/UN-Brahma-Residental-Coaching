<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'organization_id',
        'organization_subscription_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'amount',
        'status',
        'paid_at',
        'payment_mode',
        'payment_details',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'payment_details' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function subscription()
    {
        return $this->belongsTo(OrganizationSubscription::class, 'organization_subscription_id');
    }
}
