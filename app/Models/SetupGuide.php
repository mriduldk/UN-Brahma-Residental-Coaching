<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetupGuide extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'has_academic_session',
        'has_classes',
        'has_students',
        'has_question_bank',
        'has_questions',
        'has_exams',
        'has_mapped_questions'
    ];

    protected $casts = [
        'has_academic_session' => 'boolean',
        'has_classes' => 'boolean',
        'has_students' => 'boolean',
        'has_question_bank' => 'boolean',
        'has_questions' => 'boolean',
        'has_exams' => 'boolean',
        'has_mapped_questions' => 'boolean'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
