<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\Leave;
use App\Models\School\SchoolStaff;
use App\Models\School\SchoolEnrollment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $query = Leave::where('organization_id', $organizationId)->with(['leavable.student', 'leavable.schoolClass', 'leavable.section', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('leave_type', $request->type);
        }

        if ($request->filled('applicant_type')) {
            $type = $request->applicant_type === 'staff'
                ? \App\Models\School\SchoolStaff::class
                : \App\Models\School\SchoolEnrollment::class;
            $query->where('leavable_type', $type);
        }

        $leaves = $query->latest()->paginate(15);

        return view('school.leaves.index', compact('leaves'));
    }

    public function show($id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $leave = Leave::where('organization_id', $organizationId)
            ->with(['leavable.student', 'leavable.schoolClass', 'leavable.section', 'approver'])
            ->findOrFail($id);

        return view('school.leaves.show', compact('leave'));
    }

    public function create()
    {
        $organizationId = auth()->user()->currentOrganization()->id;

        $staffs = SchoolStaff::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->get();

        $students = SchoolEnrollment::whereHas('academicYear', function ($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })
            ->with(['student'])
            ->get()
            ->unique('student_id');

        return view('school.leaves.create', compact('staffs', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leavable_id' => 'required',
            'leavable_type' => 'required|in:staff,student',
            'leave_type' => 'required|in:Sick,Casual,Emergency,Annual,Maternity,Paternity,Others',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $organizationId = auth()->user()->currentOrganization()->id;

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $totalDays = $start->diffInDays($end) + 1;

        $leavableType = $request->leavable_type === 'staff'
            ? \App\Models\School\SchoolStaff::class
            : \App\Models\School\SchoolEnrollment::class;

        Leave::create([
            'organization_id' => $organizationId,
            'leavable_id' => $request->leavable_id,
            'leavable_type' => $leavableType,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'status' => 'approved', // Admin directly adding is auto-approved
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('school.leaves.index')->with('success', 'Leave record added successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        $organizationId = auth()->user()->currentOrganization()->id;
        $leave = Leave::where('organization_id', $organizationId)->findOrFail($id);

        $leave->update([
            'status' => $request->status,
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Leave status updated successfully.');
    }

    public function destroy($id)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $leave = Leave::where('organization_id', $organizationId)->findOrFail($id);

        $leave->delete();

        return back()->with('success', 'Leave record deleted successfully.');
    }
    public function personalIndex()
    {
        $user = auth()->user();
        $leavable = $this->resolveLeavable($user);

        if (!$leavable) {
            return redirect()->route('school.dashboard')->with('error', 'Unable to resolve your profile for leave management.');
        }

        $leaves = Leave::where('leavable_id', $leavable->id)
            ->where('leavable_type', get_class($leavable))
            ->latest()
            ->get();

        $view = 'school.leaves.personal.index';
        if ($user->role === 'student') {
            $view = 'student.leaves.index';
        }

        return view($view, compact('leaves', 'leavable'));
    }

    public function personalCreate()
    {
        $user = auth()->user();
        $leavable = $this->resolveLeavable($user);

        if (!$leavable) {
            return redirect()->route('school.leaves.personal.index')->with('error', 'Unauthorized access.');
        }

        $view = 'school.leaves.personal.create';
        if ($user->role === 'student') {
            $view = 'student.leaves.create';
        }

        return view($view, compact('leavable'));
    }

    public function personalShow($id)
    {
        $user = auth()->user();
        $leavable = $this->resolveLeavable($user);
        
        if (!$leavable) {
            return redirect()->route('school.dashboard')->with('error', 'Unauthorized access.');
        }

        $leave = Leave::where('leavable_id', $leavable->id)
            ->where('leavable_type', get_class($leavable))
            ->with(['approver'])
            ->findOrFail($id);

        $view = 'school.leaves.personal.show';
        if ($user->role === 'student') {
            $view = 'student.leaves.show';
        }

        return view($view, compact('leave', 'leavable'));
    }

    public function personalStore(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:Sick,Casual,Emergency,Annual,Maternity,Paternity,Others',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $user = auth()->user();
        $leavable = $this->resolveLeavable($user);

        if (!$leavable) {
            return back()->with('error', 'Failed to resolve leave association.');
        }

        $organizationId = $user->currentOrganization()->id;

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $totalDays = $start->diffInDays($end) + 1;

        Leave::create([
            'organization_id' => $organizationId,
            'leavable_id' => $leavable->id,
            'leavable_type' => get_class($leavable),
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('school.leaves.personal.index')->with('success', 'Leave application submitted successfully.');
    }

    /**
     * Resolve the leavable context (Staff or Enrollment) for the given user.
     */
    private function resolveLeavable($user)
    {
        // 1. Check if user is Staff
        $staff = SchoolStaff::where('user_id', $user->id)->first();
        if ($staff) {
            return $staff;
        }

        // 2. Check if user is Student (find enrollment)
        if ($user->role === 'student' && $user->student) {
            $enrollment = $user->student->enrollments()
                ->where('status', 'active')
                ->first();

            if ($enrollment) {
                return $enrollment;
            }
        }

        return null;
    }
}
