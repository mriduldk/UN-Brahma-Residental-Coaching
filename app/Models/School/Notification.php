<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'template_id',
        'title',
        'content',
        'channel',
        'recipient_type',
        'recipient_count',
        'status',
    ];

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class);
    }

    public function logs()
    {
        return $this->hasMany(NotificationLog::class);
    }
}
