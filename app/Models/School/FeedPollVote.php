<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FeedPollVote extends Model
{
    protected $table = 'school_feed_poll_votes';

    protected $fillable = [
        'feed_id',
        'poll_option_id',
        'user_id',
    ];

    public function feed()
    {
        return $this->belongsTo(SchoolFeed::class, 'feed_id');
    }

    public function option()
    {
        return $this->belongsTo(FeedPollOption::class, 'poll_option_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
