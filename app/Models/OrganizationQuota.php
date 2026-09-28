<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class OrganizationQuota extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'available_forms',
        'used_forms',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
