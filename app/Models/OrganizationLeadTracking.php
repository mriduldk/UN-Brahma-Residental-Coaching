<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationLeadTracking extends Model
{
    protected $fillable = [
        'organization_id',
        'call_status',
        'lead_temperature',
        'next_followup_at',
        'remarks',
        'last_called_by',
        'last_called_at',
    ];

    protected $casts = [
        'next_followup_at' => 'datetime',
        'last_called_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'last_called_by');
    }
}
