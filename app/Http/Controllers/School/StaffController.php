<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School\SchoolStaff;
use App\Models\School\StaffSubjectMapping;
use App\Models\School\AcademicYear;
use App\Models\School\SchoolClass;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $query = SchoolStaff::where('organization_id', $organizationId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('designation')) {
            $query->where('designation', $request->designation);
        }

        $staffs = $query->latest()->paginate(15)->withQueryString();
        
        $departments = SchoolStaff::where('organization_id', $organizationId)->whereNotNull('department')->distinct()->pluck('department');
        $designations = SchoolStaff::where('organization_id', $organizationId)->whereNotNull('designation')->distinct()->pluck('designation');

        $academicYears = AcademicYear::where('organization_id', $organizationId)->get();
        $classes = SchoolClass::where('organization_id', $organizationId)->with('sections')->get();

        return view('school.staff.index', compact('staffs', 'departments', 'designations', 'academicYears', 'classes'));
    }

    public function create()
    {
        return view('school.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'nullable|string|max:50|unique:school_staff,employee_id',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'gender' => 'nullable|string|max:20',
            'joining_date' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['organization_id'] = auth()->user()->currentOrganization()->id;
        $data['is_active'] = $request->has('is_active');

        SchoolStaff::create($data);

        return redirect()->route('school.staff.index')->with('success', 'Staff member added successfully.');
    }

    public function show($id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $staff = SchoolStaff::where('organization_id', $organizationId)->findOrFail($id);
        
        $academicYears = AcademicYear::where('organization_id', $organizationId)->get();
        $classes = SchoolClass::where('organization_id', $organizationId)->with('sections')->get();
        
        $mappings = StaffSubjectMapping::with(['academicYear', 'schoolClass', 'section'])
                        ->where('school_staff_id', $staff->id)
                        ->latest()
                        ->get();

        return view('school.staff.show', compact('staff', 'academicYears', 'classes', 'mappings'));
    }

    public function edit($id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $staff = SchoolStaff::where('organization_id', $organizationId)->findOrFail($id);

        return view('school.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $staff = SchoolStaff::where('organization_id', $organizationId)->findOrFail($id);

        $request->validate([
            'employee_id' => 'nullable|string|max:50|unique:school_staff,employee_id,' . $staff->id,
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'gender' => 'nullable|string|max:20',
            'joining_date' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $staff->update($data);

        return redirect()->route('school.staff.show', $staff->id)->with('success', 'Staff details updated successfully.');
    }

    public function destroy($id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $staff = SchoolStaff::where('organization_id', $organizationId)->findOrFail($id);
        $staff->delete();

        return redirect()->route('school.staff.index')->with('success', 'Staff member removed successfully.');
    }

    public function assignMapping(Request $request, $id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $staff = SchoolStaff::where('organization_id', $organizationId)->findOrFail($id);

        $request->validate([
            'academic_year_id' => 'required|exists:school_academic_years,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'subject_name' => 'nullable|string|max:255',
        ]);

        StaffSubjectMapping::create([
            'school_staff_id' => $staff->id,
            'academic_year_id' => $request->academic_year_id,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'subject_name' => $request->subject_name,
            'is_active' => true,
        ]);

        return back()->with('success', 'Mapping added successfully.');
    }

    public function removeMapping($mappingId)
    {
        $mapping = StaffSubjectMapping::findOrFail($mappingId);
        
        // Security check: Make sure this mapping belongs to a staff in the organization
        $organizationId = auth()->user()->currentOrganization()->id;
        if ($mapping->staff->organization_id !== $organizationId) {
            abort(403);
        }

        $mapping->delete();

        return back()->with('success', 'Mapping removed successfully.');
    }
}
