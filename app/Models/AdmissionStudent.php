<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionStudent extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'admission_code',
        'application_code',
        'student_application_id',
        'application_form_id',
        'student_id',
        'institution_id',
        'academic_session',
        'payment_type',
        'status',
        'total_payable_amount',
        'total_paid_amount',
        'pending_amount',
        'next_payment_date',
        'emi_months',
        'emi_amount',
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

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function admission_payments()
    {
        return $this->hasMany(AdmissionPayment::class, 'student_application_id', 'student_application_id');
    }
}
