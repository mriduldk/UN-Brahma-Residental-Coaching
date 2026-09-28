<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use SoftDeletes;
    use HasFactory, \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'student_id',
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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
