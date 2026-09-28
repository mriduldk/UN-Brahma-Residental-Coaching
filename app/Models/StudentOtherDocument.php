<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentOtherDocument extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
