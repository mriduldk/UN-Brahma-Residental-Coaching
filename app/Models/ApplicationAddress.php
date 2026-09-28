<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ApplicationAddress extends Model
{
    use SoftDeletes;
    use \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'student_application_id',
        'permanent_village_town',
        'permanent_street',
        'permanent_city',
        'permanent_district',
        'permanent_state',
        'permanent_country',
        'permanent_pin_code',
        'same_as_permanent',
        'present_village_town',
        'present_street',
        'present_city',
        'present_district',
        'present_state',
        'present_country',
        'present_pin_code',
    ];

    public function student_application()
    {
        return $this->belongsTo(StudentApplication::class);
    }
}
