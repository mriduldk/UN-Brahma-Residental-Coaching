<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffSubjectMapping extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'school_staff_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'subject_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function staff()
    {
        return $this->belongsTo(SchoolStaff::class, 'school_staff_id');
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
}
