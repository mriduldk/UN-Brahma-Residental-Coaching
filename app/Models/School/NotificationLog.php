<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'user_id',
        'recipient_contact',
        'status',
        'error_message',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
