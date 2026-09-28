<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class FeedPollOption extends Model
{
    protected $table = 'school_feed_poll_options';

    protected $fillable = [
        'feed_id',
        'option_text',
    ];

    public function feed()
    {
        return $this->belongsTo(SchoolFeed::class, 'feed_id');
    }

    public function votes()
    {
        return $this->hasMany(FeedPollVote::class, 'poll_option_id');
    }
}
