<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicQualification extends Model
{
    use SoftDeletes;
    use HasFactory, \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'student_id',
        'examination',
        'board_university',
        'passing_year',
        'roll_number',
        'registration_number',
        'percentage_grade',
        'medium_of_study',
        'division',
        'total_marks',
        'marks_obtained',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
