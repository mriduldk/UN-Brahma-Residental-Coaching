<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionPaymentTransaction extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'admission_payment_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'amount',
        'currency',
        'status',
        'method',
        'bank',
        'wallet',
        'vpa',
        'email',
        'contact',
        'fee',
        'tax',
        'error_code',
        'error_description',
        'json_response',
    ];

    protected $casts = [
        'json_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(AdmissionPaymentOrder::class, 'admission_payment_order_id');
    }
}
