<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School\SchoolFeed;
use App\Models\School\FeedPollOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SchoolFeedController extends Controller
{
    public function index(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) {
            return redirect()->route('dashboard')->with('error', 'Select an organization first.');
        }

        $feeds = SchoolFeed::with(['user', 'pollOptions.votes', 'likes', 'comments.user'])
            ->where('organization_id', $org->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('school.feed.index', compact('feeds'));
    }

    public function store(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) {
            return back()->with('error', 'Select an organization first.');
        }

        $request->validate([
            'content' => 'required|string|max:5000',
            'type' => 'required|in:standard,poll',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt|max:10240',
            'poll_options' => 'required_if:type,poll|array',
            'poll_options.*' => 'nullable|string|max:255',
        ], [
            'poll_options.required_if' => 'Please provide at least 2 poll choices.',
        ]);

        $imagePath = null;
        $documentPath = null;
        $documentName = null;

        // Handle Image Attachment
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('feeds/images', 'public');
        }

        // Handle Document Attachment
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentPath = $file->store('feeds/documents', 'public');
            $documentName = $file->getClientOriginalName();
        }

        DB::beginTransaction();
        try {
            $feed = SchoolFeed::create([
                'organization_id' => $org->id,
                'user_id' => auth()->id(),
                'content' => $request->content,
                'image_path' => $imagePath,
                'document_path' => $documentPath,
                'document_name' => $documentName,
                'type' => $request->type,
            ]);

            // Save poll options if it is a poll type feed
            if ($request->type === 'poll' && is_array($request->poll_options)) {
                $validOptionsCount = 0;
                foreach ($request->poll_options as $optionText) {
                    if (trim($optionText) !== '') {
                        $feed->pollOptions()->create([
                            'option_text' => trim($optionText),
                        ]);
                        $validOptionsCount++;
                    }
                }

                if ($validOptionsCount < 2) {
                    throw new \Exception('Please provide at least 2 valid non-empty options for the poll.');
                }
            }

            DB::commit();
            return back()->with('success', 'Announcement published successfully on the school feed!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded files on failure
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            if ($documentPath) Storage::disk('public')->delete($documentPath);

            return back()->withInput()->with('error', 'Failed to publish post: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) {
            return back()->with('error', 'Select an organization first.');
        }

        $feed = SchoolFeed::where('organization_id', $org->id)
            ->where('id', $id)
            ->firstOrFail();

        // Delete physical files
        if ($feed->image_path) {
            Storage::disk('public')->delete($feed->image_path);
        }
        if ($feed->document_path) {
            Storage::disk('public')->delete($feed->document_path);
        }

        $feed->delete();

        return back()->with('success', 'Announcement deleted successfully.');
    }

    public function storeComment(Request $request, $id)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) {
            return back()->with('error', 'Select an organization first.');
        }

        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        $feed = SchoolFeed::where('organization_id', $org->id)->findOrFail($id);

        $feed->comments()->create([
            'user_id' => auth()->id(),
            'comment' => trim($request->comment)
        ]);

        return back()->with('success', 'Comment posted successfully!');
    }
}
