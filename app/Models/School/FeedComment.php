<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FeedComment extends Model
{
    protected $table = 'school_feed_comments';

    protected $fillable = [
        'feed_id',
        'user_id',
        'comment',
    ];

    public function feed()
    {
        return $this->belongsTo(SchoolFeed::class, 'feed_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
