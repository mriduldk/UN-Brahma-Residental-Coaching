<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\AcademicYear;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    public function index()
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $sessions = AcademicYear::where('organization_id', $organizationId)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('school.sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $organizationId = auth()->user()->currentOrganization()->id;

        // If this is the first session, make it active
        $isFirst = !AcademicYear::where('organization_id', $organizationId)->exists();

        AcademicYear::create([
            'organization_id' => $organizationId,
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $isFirst,
        ]);

        if ($isFirst) {
            app(\App\Services\SetupGuideService::class)->updateStep($organizationId, 'has_academic_session', true);
        }

        return redirect()->back()->with('success', 'Academic session created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $organizationId = auth()->user()->currentOrganization()->id;
        $session = AcademicYear::where('organization_id', $organizationId)->findOrFail($id);

        $session->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->back()->with('success', 'Academic session updated successfully!');
    }

    public function destroy($id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $session = AcademicYear::where('organization_id', $organizationId)->findOrFail($id);
        
        if ($session->is_active) {
            return redirect()->back()->with('error', 'Cannot delete the active session.');
        }

        $session->delete();

        return redirect()->back()->with('success', 'Academic session deleted successfully!');
    }

    public function setActive($id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        // Deactivate all other sessions for this organization
        AcademicYear::where('organization_id', $organizationId)
            ->update(['is_active' => false]);

        // Activate the selected session
        $session = AcademicYear::where('organization_id', $organizationId)->findOrFail($id);
        $session->update(['is_active' => true]);

        app(\App\Services\SetupGuideService::class)->updateStep($organizationId, 'has_academic_session', true);

        return redirect()->back()->with('success', "Session '{$session->name}' is now active.");
    }
}
