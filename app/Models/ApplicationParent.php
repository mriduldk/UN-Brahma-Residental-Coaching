<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ApplicationParent extends Model
{
    use SoftDeletes;
    use \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'student_application_id',
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

    public function student_application()
    {
        return $this->belongsTo(StudentApplication::class);
    }
}
