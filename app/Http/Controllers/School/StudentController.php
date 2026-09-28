<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Institution;
use App\Models\School\AcademicYear;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolEnrollment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function create()
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $academicYears = AcademicYear::where('organization_id', $organizationId)->get();
        $classes = SchoolClass::where('organization_id', $organizationId)->with('sections')->get();
        
        return view('school.student.create', compact('academicYears', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:users,mobile_number',
            'email' => 'required|email|max:255|unique:users,email',
            'academic_year_id' => 'required|exists:school_academic_years,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
        ]);

        try {
            DB::beginTransaction();

            // Create global User record (Default password: phone number or default string)
            $password = $request->phone_number; // Use phone as default password
            
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'mobile_number' => $request->phone_number,
                'password' => Hash::make($password),
                'role' => 'student',
            ]);

            // Create specific Student mapping profile
            $student = Student::create([
                'user_id' => $user->id,
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
            ]);

            // Create Enrollment Mapping
            SchoolEnrollment::create([
                'student_id' => $student->id,
                'academic_year_id' => $request->academic_year_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'status' => 'active',
            ]);

            app(\App\Services\SetupGuideService::class)->updateStep(auth()->user()->currentOrganization()->id, 'has_students', true);

            DB::commit();

            return redirect()->route('school.enrollment.mapped')->with('success', 'Student created and mapped successfully. Default password is their phone number.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating student account: ' . $e->getMessage())->withInput();
        }
    }
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:school_enrollments,id',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:users,mobile_number,' . $student->user_id,
            'email' => 'required|email|max:255|unique:users,email,' . $student->user_id,
            'academic_year_id' => 'required|exists:school_academic_years,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update User record
            if ($student->user_id) {
                $user = User::findOrFail($student->user_id);
                $user->update([
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'mobile_number' => $request->phone_number,
                ]);
            }

            // 2. Update Student record
            $student->update([
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
            ]);

            // 3. Update Enrollment record
            $enrollment = SchoolEnrollment::where('id', $request->enrollment_id)
                ->where('student_id', $student->id)
                ->firstOrFail();

            $enrollment->update([
                'academic_year_id' => $request->academic_year_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Student and enrollment details updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating student: ' . $e->getMessage());
        }
    }
}
