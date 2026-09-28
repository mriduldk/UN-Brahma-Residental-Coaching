<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;

class SchoolFeeConfig extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'academic_year_id',
        'class_id',
        'monthly_amount',
        'allow_student_payment',
        'description',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
