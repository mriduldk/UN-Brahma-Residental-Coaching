<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ApplicationAcademicQualification extends Model
{
    use SoftDeletes;
    use \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'student_application_id',
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

    public function student_application()
    {
        return $this->belongsTo(StudentApplication::class);
    }
}
