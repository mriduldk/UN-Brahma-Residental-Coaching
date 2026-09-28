<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;
use App\Models\Student;

class SchoolFeePayment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'academic_year_id',
        'student_id',
        'enrollment_id',
        'month',
        'year',
        'amount_paid',
        'payment_date',
        'payment_method',
        'transaction_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'status',
        'remarks',
        'payment_payload',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'payment_payload' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(SchoolEnrollment::class, 'enrollment_id');
    }
}
