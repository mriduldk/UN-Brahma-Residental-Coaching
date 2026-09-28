<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolSection;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $classes = SchoolClass::where('organization_id', $organizationId)
            ->with('sections')
            ->orderBy('name')
            ->get();

        return view('school.classes.index', compact('classes'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);

        $organizationId = auth()->user()->currentOrganization()->id;

        SchoolClass::create([
            'organization_id' => $organizationId,
            'name' => $request->name,
            'code' => $request->code,
        ]);

        app(\App\Services\SetupGuideService::class)->updateStep($organizationId, 'has_classes', true);

        return redirect()->back()->with('success', 'Class created successfully!');
    }

    public function updateClass(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);

        $organizationId = auth()->user()->currentOrganization()->id;
        $class = SchoolClass::where('organization_id', $organizationId)->findOrFail($id);

        $class->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->back()->with('success', 'Class updated successfully!');
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'name' => 'required|string|max:255',
            'room_number' => 'nullable|string|max:50',
        ]);

        SchoolSection::create([
            'class_id' => $request->class_id,
            'name' => $request->name,
            'room_number' => $request->room_number,
        ]);

        return redirect()->back()->with('success', 'Section added successfully!');
    }

    public function destroyClass($id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $class = SchoolClass::where('organization_id', $organizationId)->findOrFail($id);
        $class->delete();

        return redirect()->back()->with('success', 'Class and its sections removed.');
    }

    public function destroySection($id)
    {
        $section = SchoolSection::findOrFail($id);
        // Better check if the section belongs to a class of the current organization
        if ($section->schoolClass->organization_id == auth()->user()->currentOrganization()->id) {
            $section->delete();
            return redirect()->back()->with('success', 'Section removed.');
        }
        
        return redirect()->back()->with('error', 'Unauthorized action.');
    }
}
