<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class StudentApplication extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'application_code',
        'application_form_id',
        'student_id',
        'status',
        'submitted_at',
        'payment_status',
        'transaction_id',
        'current_step',
        'approval_status',
        'approval_remarks',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function application_form()
    {
        return $this->belongsTo(ApplicationForm::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function application_student()
    {
        return $this->hasOne(ApplicationStudent::class);
    }

    public function application_address()
    {
        return $this->hasOne(ApplicationAddress::class);
    }

    public function application_parent()
    {
        return $this->hasOne(ApplicationParent::class);
    }

    public function application_academic_qualifications()
    {
        return $this->hasMany(ApplicationAcademicQualification::class);
    }

    public function application_documents()
    {
        return $this->hasOne(ApplicationDocument::class);
    }

    public function application_bank_detail()
    {
        return $this->hasOne(ApplicationBankDetail::class);
    }

    public function admission_payments()
    {
        return $this->hasMany(AdmissionPayment::class);
    }

    public function payment_orders()
    {
        return $this->hasMany(PaymentOrder::class);
    }

    public function payment_transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function latest_payment_order()
    {
        return $this->hasOne(PaymentOrder::class)->latestOfMany();
    }

    public function admission_payment_orders()
    {
        return $this->hasMany(AdmissionPaymentOrder::class);
    }

    public function admission_payment_transactions()
    {
        return $this->hasManyThrough(AdmissionPaymentTransaction::class, AdmissionPaymentOrder::class);
    }

    public function admission_student()
    {
        return $this->hasOne(AdmissionStudent::class);
    }
}
