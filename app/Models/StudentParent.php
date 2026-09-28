<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    use SoftDeletes;
    use HasFactory, \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'student_id',
        'father_name',
        'father_mobile',
        'father_email',
        'father_occupation',
        'father_qualification',
        'father_annual_income',
        'mother_name',
        'mother_mobile',
        'mother_email',
        'mother_occupation',
        'mother_qualification',
        'mother_annual_income',
        'guardian_name',
        'guardian_relation',
        'guardian_mobile',
        'guardian_address',
        'guardian_occupation',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
