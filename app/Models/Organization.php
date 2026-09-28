<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use SoftDeletes;
    use \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'user_id',
        'organization_name',
        'short_name',
        'organization_code',
        'type',
        'business_type',
        'category',
        'industry',
        'registration_no',
        'gst_no',
        'pan_no',
        'tan_no',
        'website',
        'logo',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->organization_code)) {
                // Generate code: ORG-{TIMESTAMP}-{RANDOM}
                $organization->organization_code = 'ORG-' . time() . '-' . rand(1000, 9999);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institutions()
    {
        return $this->hasMany(Institution::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    protected static function booted()
    {
        static::created(function ($organization) {
            $organization->quota()->create([
                'available_forms' => 1,
                'used_forms' => 0
            ]);

            $organization->configuration()->create([
                'is_registration_fee_enabled' => true,
                'is_admission_enabled' => false
            ]);
        });
    }

    public function applicationForms()
    {
        return $this->hasMany(ApplicationForm::class);
    }

    public function quota()
    {
        return $this->hasOne(OrganizationQuota::class);
    }

    public function configuration()
    {
        return $this->hasOne(OrganizationConfiguration::class);
    }

    public function getAvailableFormsCount()
    {
        return $this->quota ? $this->quota->available_forms : 1;
    }

    public function getUsedFormsCount()
    {
        return $this->quota ? $this->quota->used_forms : 0;
    }

    public function canCreateForm()
    {
        return true;
    }

    public function subscriptionPayments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function leadTracking()
    {
        return $this->hasOne(OrganizationLeadTracking::class);
    }
}
