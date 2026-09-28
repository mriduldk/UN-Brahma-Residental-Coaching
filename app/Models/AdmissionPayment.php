<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionPayment extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'student_application_id',
        'payment_type', // full, emi, cash
        'total_amount',
        'discount_value',
        'discount_type',
        'final_amount',
        'paid_amount',
        'emi_months',
        'payer_name',
        'payer_phone',
        'receiver_name',
        'received_by',
        'transaction_id',
        'status',
        'remarks',
        'is_settled',
        'settled_at',
        'dd_number',
        'dd_date',
        'dd_bank',
        'dd_attachment_path',
    ];

    public function student_application()
    {
        return $this->belongsTo(StudentApplication::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function online_orders()
    {
        return $this->hasMany(AdmissionPaymentOrder::class, 'admission_payment_id');
    }

    public function order()
    {
        return $this->hasOne(AdmissionPaymentOrder::class, 'admission_payment_id')->latestOfMany();
    }
}
