<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'created_by',
        'organization_id'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
