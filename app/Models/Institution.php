<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'institution_name',
        'institution_code',
        'contact_number',
        'email',
        'address',
        'bank_account_no',
        'bank_account_holder_name',
        'bank_name',
        'bank_ifsc_code',
        'bank_branch_name',
        'razorpay_key',
        'razorpay_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'razorpay_key' => 'encrypted',
        'razorpay_secret' => 'encrypted',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function application_forms()
    {
        return $this->hasMany(ApplicationForm::class);
    }

    public function admission_students()
    {
        return $this->hasMany(AdmissionStudent::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'institution_user', 'institution_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
}
