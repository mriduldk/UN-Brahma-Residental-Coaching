<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Organization;

class SchoolFeed extends Model
{
    use SoftDeletes;

    protected $table = 'school_feeds';

    protected $fillable = [
        'organization_id',
        'user_id',
        'content',
        'image_path',
        'document_path',
        'document_name',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function pollOptions()
    {
        return $this->hasMany(FeedPollOption::class, 'feed_id');
    }

    public function likes()
    {
        return $this->hasMany(FeedLike::class, 'feed_id');
    }

    public function pollVotes()
    {
        return $this->hasMany(FeedPollVote::class, 'feed_id');
    }

    public function comments()
    {
        return $this->hasMany(FeedComment::class, 'feed_id');
    }

    /**
     * Check if a specific user liked this post
     */
    public function isLikedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Check if a specific user voted on this poll
     */
    public function userVote($userId)
    {
        return $this->pollVotes()->where('user_id', $userId)->first();
    }
}
