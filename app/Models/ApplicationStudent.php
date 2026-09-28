<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ApplicationStudent extends Model
{
    use SoftDeletes;
    use \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'student_application_id',
        'full_name',
        'is_pwd',
        'employment_exchange_no',
        'is_permanent_resident_of_assam',
        'is_btc_resident',
        'phone_number',
        'email',
        'dob',
        'age',
        'gender',
        'blood_group',
        'category',
        'religion',
        'nationality',
        'mother_tongue',
        'identification_mark',
        'marital_status',
        'alternate_mobile_number',
        'is_ex_serviceman',
        'ncc_certificate',
        'has_national_sports_achievement',
        'has_fine_arts_cultural_achievement',
    ];

    protected $casts = [
        'dob' => 'date',
        'age' => 'integer',
        'is_pwd' => 'boolean',
        'is_permanent_resident_of_assam' => 'boolean',
        'is_btc_resident' => 'boolean',
        'is_ex_serviceman' => 'boolean',
        'has_national_sports_achievement' => 'boolean',
        'has_fine_arts_cultural_achievement' => 'boolean',
    ];

    public function student_application()
    {
        return $this->belongsTo(StudentApplication::class);
    }
}
