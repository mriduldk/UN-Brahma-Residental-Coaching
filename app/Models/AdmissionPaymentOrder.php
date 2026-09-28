<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionPaymentOrder extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'student_application_id',
        'application_form_id',
        'student_id',
        'admission_payment_id',
        'user_id',
        'razorpay_order_id',
        'amount',
        'amount_paid',
        'currency',
        'status',
        'attempts',
        'payment_category',
        'receipt',
        'notes',
        'json_response',
    ];

    protected $casts = [
        'notes' => 'array',
        'json_response' => 'array',
    ];

    public function student_application()
    {
        return $this->belongsTo(StudentApplication::class);
    }

    public function application_form()
    {
        return $this->belongsTo(ApplicationForm::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function admission_payment()
    {
        return $this->belongsTo(AdmissionPayment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(AdmissionPaymentTransaction::class);
    }

    public function latest_transaction()
    {
        return $this->hasOne(AdmissionPaymentTransaction::class)->latestOfMany();
    }
}
