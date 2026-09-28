<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'payment_order_id',
        'student_application_id',
        'razorpay_payment_id',
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
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'json_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(PaymentOrder::class, 'payment_order_id');
    }

    public function application()
    {
        return $this->belongsTo(StudentApplication::class, 'student_application_id');
    }
}
