<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use SoftDeletes;
    use HasFactory, \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'dob',
        'age',
        'blood_group',
        'category',
        'is_pwd',
        'religion',
        'community',
        'nationality',
        'mother_tongue',
        'identification_mark',
        'employment_exchange_no',
        'is_permanent_resident_of_assam',
        'is_btc_resident',
        'marital_status',
        'phone_number',
        'alternate_mobile_number',
        'email',
        'is_ex_serviceman',
        'ncc_certificate',
        'has_national_sports_achievement',
        'has_fine_arts_cultural_achievement',
    ];

    /**
     * Backwards compatibility for nationality attribute
     */
    public function getNationalityAttribute()
    {
        return $this->attributes['community'] ?? $this->attributes['nationality'] ?? null;
    }

    public function setNationalityAttribute($value)
    {
        $this->attributes['community'] = $value;
    }

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function academic_qualifications()
    {
        return $this->hasMany(AcademicQualification::class);
    }

    public function parent()
    {
        return $this->hasOne(StudentParent::class);
    }

    public function bank_detail()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function document()
    {
        return $this->hasOne(StudentDocument::class);
    }

    public function otherDocuments()
    {
        return $this->hasMany(StudentOtherDocument::class);
    }

    public function enrollments()
    {
        return $this->hasMany(School\SchoolEnrollment::class);
    }

    public function getCurrentEnrollmentAttribute()
    {
        return $this->enrollments()->where('status', 'active')->first();
    }

    public function homeworkSubmissions()
    {
        return $this->hasMany(HomeworkSubmission::class);
    }
}
