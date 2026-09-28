<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'channel',
        'subject',
        'content',
        'is_active',
    ];

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }
}
