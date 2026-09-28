<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeworkSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'homework_id',
        'student_id',
        'submission_text',
        'attachment',
        'status',
        'remarks',
        'marks_obtained',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
