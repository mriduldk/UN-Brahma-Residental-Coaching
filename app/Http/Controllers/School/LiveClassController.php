<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\LiveClass;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolStaff;
use App\Models\School\AcademicYear;
use App\Models\School\StaffSubjectMapping;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LiveClassController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    public function index()
    {
        $user = auth()->user();
        $organization = $user->currentOrganization();
        
        if (!$organization) {
            return back()->with('error', 'You are not currently associated with any active organization or enrollment.');
        }

        $orgId = $organization->id;
        
        $activeYear = AcademicYear::where('organization_id', $orgId)
            ->where('is_active', true)
            ->first();

        $activeYearId = $activeYear ? $activeYear->id : null;

        // Determine if teacher or student
        $staff = SchoolStaff::where('user_id', $user->id)->first();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        $isAdmin = ($user->role === 'administration' || $user->role === 'school');

        $query = LiveClass::where('organization_id', $orgId)
            ->where('academic_year_id', $activeYearId)
            ->with(['schoolClass', 'section', 'staff'])
            ->orderBy('start_time', 'desc');

        if ($isAdmin || $staff) {
            // Admins see all classes, Staff see their own
            if (!$isAdmin) {
                $query->where('staff_id', $staff->id);
            }
            
            $liveClasses = $query->paginate(15);
            $classes = SchoolClass::where('organization_id', $orgId)->get();
            
            // For subjects, if staff, use their mappings. If admin, show all active subjects or a default list
            if ($staff) {
                $subjects = StaffSubjectMapping::where('school_staff_id', $staff->id)
                    ->where('academic_year_id', $activeYearId)
                    ->pluck('subject_name')
                    ->unique();
            } else {
                // Admin might want to schedule for any subject
                $subjects = StaffSubjectMapping::where('academic_year_id', $activeYearId)
                    ->pluck('subject_name')
                    ->unique();
                if ($subjects->isEmpty()) {
                    $subjects = collect(['General', 'Mathematics', 'Science', 'English', 'Social Studies']);
                }
            }

            return view('school.live-classes.index', compact('liveClasses', 'classes', 'subjects', 'staff', 'isAdmin'));
        } elseif ($student) {
            // Students see classes for their enrollment
            $enrollment = \App\Models\School\SchoolEnrollment::where('student_id', $student->id)
                ->where('academic_year_id', $activeYearId)
                ->where('status', 'active')
                ->first();

            if (!$enrollment) {
                return view('school.live-classes.student-index', ['liveClasses' => collect(), 'error' => 'No active enrollment found.']);
            }

            $liveClasses = $query->where('class_id', $enrollment->class_id)
                ->where(function($q) use ($enrollment) {
                    $q->whereNull('section_id')->orWhere('section_id', $enrollment->section_id);
                })
                ->paginate(15);

            return view('school.live-classes.student-index', compact('liveClasses'));
        }

        return view('school.live-classes.index', compact('liveClasses', 'classes', 'subjects', 'staff', 'isAdmin'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $organization = $user->currentOrganization();

        if (!$organization) {
            return back()->with('error', 'Unable to identify your current organization.');
        }

        $orgId = $organization->id;
        $staff = SchoolStaff::where('user_id', $user->id)->first();
        $isAdmin = ($user->role === 'administration');

        if (!$isAdmin && !$staff) {
            abort(403, 'Only staff or administrators can schedule live classes.');
        }
        
        $activeYear = AcademicYear::where('organization_id', $orgId)
            ->where('is_active', true)
            ->firstOrFail();

        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'subject_name' => 'required|string',
            'topic' => 'required|string',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:300',
        ]);

        try {
            // Create Zoom Meeting via Service
            $zoomData = $this->zoomService->createMeeting($request->all(), $orgId);

            // Resolve Staff ID: Use current user's staff record if available, 
            // otherwise use a fallback or require one.
            $finalStaffId = $staff ? $staff->id : ($request->staff_id ?? SchoolStaff::where('organization_id', $orgId)->value('id'));

            if (!$finalStaffId) {
                return back()->with('error', 'Unable to assign a staff member to this class. Please add staff first.');
            }

            LiveClass::create([
                'organization_id' => $orgId,
                'academic_year_id' => $activeYear->id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'staff_id' => $finalStaffId,
                'subject_name' => $request->subject_name,
                'topic' => $request->topic,
                'meeting_id' => $zoomData['meeting_id'],
                'start_url' => $zoomData['start_url'],
                'join_url' => $zoomData['join_url'],
                'password' => $zoomData['password'],
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'status' => 'scheduled',
            ]);

            return back()->with('success', 'Live class scheduled successfully.');
        } catch (\Exception $e) {
            Log::error('Live Class Creation Failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to schedule live class: ' . $e->getMessage());
        }
    }

    public function join($id)
    {
        $user = auth()->user();
        $liveClass = LiveClass::findOrFail($id);
        
        // Security Check
        $staff = SchoolStaff::where('user_id', $user->id)->first();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        $isAdmin = ($user->role === 'administration');

        // Allow Admin or the assigned Staff (Teacher) to join/start
        if ($isAdmin || ($staff && $staff->id === $liveClass->staff_id)) {
            // Host/Admin Join
            return redirect($liveClass->start_url ?: $liveClass->join_url);
        }

        if ($student) {
            $enrollment = \App\Models\School\SchoolEnrollment::where('student_id', $student->id)
                ->where('class_id', $liveClass->class_id)
                ->where('status', 'active')
                ->first();

            if ($enrollment) {
                // Check section if applicable
                if ($liveClass->section_id && $liveClass->section_id !== $enrollment->section_id) {
                    abort(403, 'Unauthorized section.');
                }
                
                return redirect($liveClass->join_url);
            }
        }

        abort(403, 'Unauthorized to join this live class.');
    }

    public function update(\App\Services\ZoomService $zoomService, Request $request, $id)
    {
        $liveClass = LiveClass::findOrFail($id);
        
        $user = auth()->user();
        $isAdmin = $user->role === 'administration';
        $staff = \App\Models\School\SchoolStaff::where('user_id', $user->id)->first();
        
        if (!$isAdmin && $liveClass->staff_id !== $staff?->id) {
            abort(403);
        }

        $request->validate([
            'topic' => 'required|string|max:255',
            'start_time' => 'required|date',
            'duration' => 'required|integer|min:15',
            'manual_meeting_url' => 'nullable|url',
        ]);

        $liveClass->update([
            'topic' => $request->topic,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
            'meeting_url' => $request->manual_meeting_url ?? $liveClass->meeting_url,
            'meeting_id' => $request->manual_meeting_id ?? $liveClass->meeting_id,
            'password' => $request->manual_password ?? $liveClass->password,
        ]);

        // Sync with Zoom if not manual
        $zoomService->updateMeeting($liveClass->meeting_id, [
            'topic' => $liveClass->topic,
            'start_time' => $liveClass->start_time,
            'duration' => $liveClass->duration,
        ]);

        return back()->with('success', 'Schedule updated successfully.');
    }

    public function destroy($id)
    {
        $liveClass = LiveClass::findOrFail($id);
        
        $user = auth()->user();
        $isAdmin = $user->role === 'administration';
        $staff = \App\Models\School\SchoolStaff::where('user_id', $user->id)->first();
        
        if (!$isAdmin && $liveClass->staff_id !== $staff?->id) {
            abort(403);
        }

        $liveClass->update(['status' => 'cancelled']);
        return back()->with('success', 'Live class cancelled.');
    }
}
