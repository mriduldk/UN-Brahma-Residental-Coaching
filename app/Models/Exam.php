<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'question_bank_id',
        'title',
        'description',
        'duration_minutes',
        'start_time',
        'end_time',
        'negative_marks_per_wrong',
        'total_marks',
        'passing_marks',
        'created_by',
        'is_active',
        'max_attempts',
        'academic_year_id',
        'school_class_id',
        'school_section_id',
        'organization_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function bank()
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_question')->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function academicYear()
    {
        return $this->belongsTo(School\AcademicYear::class, 'academic_year_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(School\SchoolClass::class, 'school_class_id');
    }

    public function schoolSection()
    {
        return $this->belongsTo(School\SchoolSection::class, 'school_section_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
