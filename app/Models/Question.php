<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'question_bank_id',
        'exam_id',
        'question_text',
        'marks',
        'is_previous_year',
        'previous_year'
    ];

    public function bank()
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_question')->withTimestamps();
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
