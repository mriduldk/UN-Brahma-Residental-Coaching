<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'student_application_id',
        'razorpay_order_id',
        'amount',
        'amount_paid',
        'amount_due',
        'currency',
        'receipt',
        'status',
        'attempts',
        'notes',
        'json_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'notes' => 'array',
        'json_response' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(StudentApplication::class, 'student_application_id');
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
