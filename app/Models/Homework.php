<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Homework extends Model
{
    use SoftDeletes;

    protected $table = 'homeworks';

    protected $fillable = [
        'organization_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'subject_name',
        'type',
        'title',
        'description',
        'attachment',
        'homework_date',
        'due_date',
        'created_by',
    ];

    protected $casts = [
        'homework_date' => 'date',
        'due_date' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(\App\Models\School\AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(\App\Models\School\SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(\App\Models\School\SchoolSection::class, 'section_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(HomeworkSubmission::class);
    }
}
