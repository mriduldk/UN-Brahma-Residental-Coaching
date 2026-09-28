<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolSection;
use App\Models\School\SchoolStaff;
use App\Models\School\SchoolTimetable;
use App\Models\School\AcademicYear;
use App\Models\School\StaffSubjectMapping;
use Barryvdh\DomPDF\Facade\Pdf;

class TimetableController extends Controller
{
    public function generatePdf(Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        
        $activeYear = AcademicYear::where('organization_id', $orgId)
            ->where('is_active', true)
            ->first();
        
        $selectedClassId = $request->class_id;
        $selectedSectionId = $request->section_id;

        if (!$selectedClassId) {
            return back()->with('error', 'Please select a class first.');
        }

        $classes = SchoolClass::where('organization_id', $orgId)->with('sections')->get();
        $class = $classes->find($selectedClassId);
        $section = $selectedSectionId ? $class->sections->find($selectedSectionId) : null;

        $timetable = SchoolTimetable::with(['staff', 'section'])
            ->where('organization_id', $orgId)
            ->where('academic_year_id', $activeYear?->id)
            ->where('class_id', $selectedClassId)
            ->when($selectedSectionId, function($q) use ($selectedSectionId) {
                return $q->where('section_id', $selectedSectionId);
            })
            ->get()
            ->groupBy('day_of_week');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $pdf = Pdf::loadView('school.timetable.pdf', compact(
            'class',
            'section',
            'activeYear',
            'timetable',
            'days'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("timetable_{$class->name}.pdf");
    }

    public function view(Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        
        $activeYear = AcademicYear::where('organization_id', $orgId)
            ->where('is_active', true)
            ->first();
        
        $selectedClassId = $request->class_id;
        $selectedSectionId = $request->section_id;

        $classes = SchoolClass::where('organization_id', $orgId)->with('sections')->get();
        if (!$selectedClassId && $classes->count() > 0) {
            $selectedClassId = $classes->first()->id;
        }

        $timetable = [];
        
        if ($selectedClassId && $activeYear) {
            $timetable = SchoolTimetable::with(['staff', 'section'])
                ->where('organization_id', $orgId)
                ->where('academic_year_id', $activeYear->id)
                ->where('class_id', $selectedClassId)
                ->when($selectedSectionId, function($q) use ($selectedSectionId) {
                    return $q->where('section_id', $selectedSectionId);
                })
                ->get()
                ->groupBy('day_of_week');
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('school.timetable.view', compact(
            'classes', 
            'activeYear', 
            'timetable', 
            'days', 
            'selectedClassId', 
            'selectedSectionId'
        ));
    }

    public function index(Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        
        $activeYear = AcademicYear::where('organization_id', $orgId)
            ->where('is_active', true)
            ->first();

        $classes = SchoolClass::where('organization_id', $orgId)->with('sections')->get();
        
        $selectedClassId = $request->class_id;
        $selectedSectionId = $request->section_id;
        
        $timetable = [];
        $mappings = [];
        
        if ($selectedClassId && $activeYear) {
            $timetable = SchoolTimetable::with(['staff', 'section'])
                ->where('organization_id', $orgId)
                ->where('academic_year_id', $activeYear->id)
                ->where('class_id', $selectedClassId)
                ->when($selectedSectionId, function($q) use ($selectedSectionId) {
                    return $q->where('section_id', $selectedSectionId);
                })
                ->get()
                ->groupBy('day_of_week');

            // Get available subjects/teachers for this class to populate the "Add" modal
            $mappings = StaffSubjectMapping::with('staff')
                ->where('academic_year_id', $activeYear->id)
                ->where('class_id', $selectedClassId)
                ->get();
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('school.timetable.index', compact(
            'classes', 
            'activeYear', 
            'timetable', 
            'days', 
            'selectedClassId', 
            'selectedSectionId',
            'mappings'
        ));
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        
        $request->validate([
            'academic_year_id' => 'required|exists:school_academic_years,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'school_staff_id' => 'required|exists:school_staff,id',
            'subject_name' => 'required|string',
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_number' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['organization_id'] = $orgId;
        
        SchoolTimetable::create($data);

        return back()->with('success', 'Timetable slot added successfully.');
    }

    public function update(Request $request, $id)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        $slot = SchoolTimetable::where('organization_id', $orgId)->findOrFail($id);
        
        $request->validate([
            'academic_year_id' => 'required|exists:school_academic_years,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'school_staff_id' => 'required|exists:school_staff,id',
            'subject_name' => 'required|string',
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_number' => 'nullable|string',
        ]);

        $slot->update($request->all());

        return back()->with('success', 'Timetable slot updated successfully.');
    }

    public function destroy($id)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        $slot = SchoolTimetable::where('organization_id', $orgId)->findOrFail($id);
        $slot->delete();

        return back()->with('success', 'Slot removed successfully.');
    }
}
