<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class QuotaPaymentTransaction extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'quota_payment_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'amount',
        'method',
        'status',
        'json_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'json_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(QuotaPaymentOrder::class, 'quota_payment_order_id');
    }
}
