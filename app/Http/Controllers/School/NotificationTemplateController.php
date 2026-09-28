<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->currentOrganization()->id;
        $templates = NotificationTemplate::where('organization_id', $orgId)->orderBy('name')->get();
        return view('school.notifications.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('school.notifications.templates.create');
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'channel' => 'required|in:sms,email,whatsapp,push',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        NotificationTemplate::create([
            'organization_id' => $orgId,
            'name' => $request->name,
            'channel' => $request->channel,
            'subject' => $request->subject,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('school.notifications.templates.index')->with('success', 'Template created successfully.');
    }

    public function edit(NotificationTemplate $template)
    {
        return view('school.notifications.templates.edit', compact('template'));
    }

    public function update(Request $request, NotificationTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'channel' => 'required|in:sms,email,whatsapp,push',
            'content' => 'required|string',
        ]);

        $template->update([
            'name' => $request->name,
            'channel' => $request->channel,
            'subject' => $request->subject,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('school.notifications.templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(NotificationTemplate $template)
    {
        $template->delete();
        return redirect()->route('school.notifications.templates.index')->with('success', 'Template deleted successfully.');
    }
}
