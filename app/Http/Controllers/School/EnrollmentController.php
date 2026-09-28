<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\AcademicYear;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolSection;
use App\Models\School\SchoolEnrollment;
use App\Models\AdmissionStudent;
use App\Models\Student;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $source = $request->input('source', 'application'); // default to 'application'

        // Get available academic years
        $academicYears = AcademicYear::where('organization_id', $organizationId)->get();
        $activeYear = $academicYears->where('is_active', true)->first();

        // Get classes and sections
        $classes = SchoolClass::where('organization_id', $organizationId)->with('sections')->get();

        // Get students who have completed admission but are NOT enrolled in the active year
        $enrolledStudentIds = [];
        if ($activeYear) {
            $enrolledStudentIds = SchoolEnrollment::where('academic_year_id', $activeYear->id)->pluck('student_id')->toArray();
        }

        $institutionIds = \App\Models\Institution::where('organization_id', $organizationId)->pluck('id');
        if ($source === 'enrolled') {
            // Students who have taken admission
            $eligibleStudents = \App\Models\AdmissionStudent::whereIn('institution_id', $institutionIds)
                ->whereIn('status', ['active', 'completed'])
                ->whereNotIn('student_id', $enrolledStudentIds)
                ->with(['student', 'application_form'])
                ->get();
        } else {
            // Students from applications
            $admittedStudentIds = \App\Models\AdmissionStudent::whereIn('institution_id', $institutionIds)->pluck('student_id')->toArray();
            
            $eligibleStudents = \App\Models\StudentApplication::whereHas('application_form', function($q) use ($institutionIds) {
                    $q->whereIn('institution_id', $institutionIds);
                })
                ->where('status', 'submitted')
                ->whereNotNull('student_id')
                ->whereNotIn('student_id', $admittedStudentIds) // Prefer the admission record if they have one
                ->whereNotIn('student_id', $enrolledStudentIds)
                ->with(['student', 'application_form'])
                ->get();
        }

        return view('school.enrollment.index', compact('academicYears', 'activeYear', 'classes', 'eligibleStudents', 'source'));
    }

    public function map(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'academic_year_id' => 'required|exists:school_academic_years,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
        ]);

        $studentIds = $request->student_ids;
        $academicYearId = $request->academic_year_id;
        $classId = $request->class_id;
        $sectionId = $request->section_id;

        $count = 0;
        foreach ($studentIds as $studentId) {
            // Check if already enrolled to avoid duplicates
            $exists = SchoolEnrollment::where('student_id', $studentId)
                ->where('academic_year_id', $academicYearId)
                ->exists();

            if (!$exists) {
                SchoolEnrollment::create([
                    'student_id' => $studentId,
                    'academic_year_id' => $academicYearId,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'status' => 'active',
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            $organizationId = auth()->user()->currentOrganization()->id;
            app(\App\Services\SetupGuideService::class)->updateStep($organizationId, 'has_students', true);
        }

        return redirect()->back()->with('success', "Successfully mapped $count students to the selected class.");
    }

    public function mapped(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        $academicYears = AcademicYear::where('organization_id', $organizationId)->get();
        $classes = SchoolClass::where('organization_id', $organizationId)->with('sections')->get();
        
        $query = SchoolEnrollment::with(['student', 'schoolClass', 'section', 'academicYear'])
            ->whereHas('academicYear', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });

        // Filters
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } else {
            // default to active year if none selected
            $activeYear = $academicYears->where('is_active', true)->first();
            if ($activeYear) {
                $query->where('academic_year_id', $activeYear->id);
            }
        }
        
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        $mappedStudents = $query->latest()->paginate(15)->withQueryString();

        return view('school.enrollment.mapped', compact('academicYears', 'classes', 'mappedStudents'));
    }
}
