<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\PublicNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicNoticeController extends Controller
{
    public function index()
    {
        $org = auth()->user()->currentOrganization();
        $notices = PublicNotice::where('organization_id', $org->id)->latest()->get();
        return view('administration.notices.index', compact('notices'));
    }

    public function create()
    {
        return view('administration.notices.form');
    }

    public function store(Request $request)
    {
        $org = auth()->user()->currentOrganization();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'is_active' => $request->boolean('is_active', true),
            'organization_id' => $org->id,
        ];

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('notices', 'public');
        }

        PublicNotice::create($data);

        return redirect()->route('administration.notices.index')->with('success', 'Notice created successfully.');
    }

    public function edit($id)
    {
        $org = auth()->user()->currentOrganization();
        $notice = PublicNotice::where('organization_id', $org->id)->findOrFail($id);
        return view('administration.notices.form', compact('notice'));
    }

    public function update(Request $request, $id)
    {
        $org = auth()->user()->currentOrganization();
        $notice = PublicNotice::where('organization_id', $org->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('document')) {
            if ($notice->document_path && Storage::disk('public')->exists($notice->document_path)) {
                Storage::disk('public')->delete($notice->document_path);
            }
            $updateData['document_path'] = $request->file('document')->store('notices', 'public');
        }

        $notice->update($updateData);

        return redirect()->route('administration.notices.index')->with('success', 'Notice updated successfully.');
    }

    public function destroy($id)
    {
        $org = auth()->user()->currentOrganization();
        $notice = PublicNotice::where('organization_id', $org->id)->findOrFail($id);

        if ($notice->document_path && Storage::disk('public')->exists($notice->document_path)) {
            Storage::disk('public')->delete($notice->document_path);
        }

        $notice->delete();

        return redirect()->route('administration.notices.index')->with('success', 'Notice deleted successfully.');
    }
}
