<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolTimetable extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'school_staff_id',
        'subject_name',
        'day_of_week',
        'start_time',
        'end_time',
        'room_number',
        'is_active',
    ];

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function staff()
    {
        return $this->belongsTo(SchoolStaff::class, 'school_staff_id');
    }
}
