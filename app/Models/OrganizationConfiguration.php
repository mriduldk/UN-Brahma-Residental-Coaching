<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class OrganizationConfiguration extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'is_registration_fee_enabled',
        'is_admission_enabled',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
