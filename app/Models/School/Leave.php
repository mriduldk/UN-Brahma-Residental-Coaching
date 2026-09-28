<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Leave extends Model
{
    use SoftDeletes;
    protected $table = 'school_leaves';

    protected $fillable = [
        'organization_id',
        'leavable_id',
        'leavable_type',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the organization that owns the leave.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the staff or student mapping that the leave belongs to.
     */
    public function leavable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the administrator who approved/rejected the leave.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
