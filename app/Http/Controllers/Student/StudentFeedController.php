<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School\SchoolFeed;
use App\Models\School\SchoolEnrollment;
use App\Models\School\FeedLike;
use App\Models\School\FeedPollVote;
use App\Models\School\FeedPollOption;

class StudentFeedController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.onboarding')->with('error', 'Please complete onboarding first.');
        }

        // Get the active school enrollment to determine organization
        $enrollment = SchoolEnrollment::with(['schoolClass'])
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment || !$enrollment->schoolClass) {
            return view('student.feed.index', [
                'enrollment' => null,
                'feeds' => collect()
            ]);
        }

        $orgId = $enrollment->schoolClass->organization_id;

        $feeds = SchoolFeed::with(['user', 'pollOptions.votes', 'likes', 'comments.user'])
            ->where('organization_id', $orgId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.feed.index', compact('enrollment', 'feeds'));
    }

    /**
     * Like/Unlike a post asynchronously
     */
    public function like(Request $request, $id)
    {
        $feed = SchoolFeed::findOrFail($id);
        $userId = auth()->id();

        $like = FeedLike::where('feed_id', $feed->id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            FeedLike::create([
                'feed_id' => $feed->id,
                'user_id' => $userId,
            ]);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $feed->likes()->count()
        ]);
    }

    /**
     * Vote on a poll
     */
    public function vote(Request $request, $id)
    {
        $request->validate([
            'poll_option_id' => 'required|exists:school_feed_poll_options,id',
        ]);

        $feed = SchoolFeed::findOrFail($id);
        $userId = auth()->id();

        // Check if user already voted
        $alreadyVoted = FeedPollVote::where('feed_id', $feed->id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyVoted) {
            return back()->with('error', 'You have already voted on this poll.');
        }

        // Verify the option belongs to this feed
        $option = FeedPollOption::where('feed_id', $feed->id)
            ->where('id', $request->poll_option_id)
            ->firstOrFail();

        FeedPollVote::create([
            'feed_id' => $feed->id,
            'poll_option_id' => $option->id,
            'user_id' => $userId,
        ]);

        return back()->with('success', 'Your vote has been submitted successfully!');
    }

    /**
     * Comment on a post
     */
    public function comment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $feed = SchoolFeed::findOrFail($id);

        $feed->comments()->create([
            'user_id' => auth()->id(),
            'comment' => trim($request->comment),
        ]);

        return back()->with('success', 'Your comment has been posted successfully!');
    }
}
