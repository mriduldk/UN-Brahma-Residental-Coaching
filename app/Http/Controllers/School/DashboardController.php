<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $org = $user->organizations()->first(); // Assuming one organization per user for now, or use context

        if (!$org) {
            return view('school.dashboard', [
                'stats' => [
                    'total_students' => 0,
                    'total_staff' => 0,
                    'active_classes' => 0,
                    'attendance_percentage' => 0,
                    'student_attendance_summary' => collect(),
                    'staff_attendance_summary' => collect(),
                    'recent_activity' => collect(),
                    'today_exceptions' => collect(),
                    'class_strength' => collect(),
                    'active_year' => null,
                ],
                'setupGuide' => (object) [
                    'has_academic_session' => false,
                    'has_classes' => false,
                    'has_students' => false,
                ]
            ]);
        }

        $orgId = $org->id;

        // 1. Get current active academic year
        $activeYear = \App\Models\School\AcademicYear::where('organization_id', $orgId)
            ->where('is_active', true)
            ->first();

        // 2. Metrics
        $totalStudents = 0;
        if ($activeYear) {
            $totalStudents = \App\Models\School\SchoolEnrollment::where('academic_year_id', $activeYear->id)
                ->where('status', 'active')
                ->count();
        }

        $totalStaff = \App\Models\School\SchoolStaff::where('organization_id', $orgId)
            ->where('is_active', true)
            ->count();

        $activeClassesCount = \App\Models\School\SchoolClass::where('organization_id', $orgId)->count();

        // 3. Today's Attendance Summary (Students)
        $today = now()->toDateString();
        $studentAttendanceToday = \App\Models\School\StudentAttendance::where('organization_id', $orgId)
            ->where('date', $today)
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $presentCount = $studentAttendanceToday->get('Present', 0);
        $totalMarked = $studentAttendanceToday->sum();
        $attendancePercentage = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100) : 0;

        // 4. Staff Attendance Summary (Today)
        $staffAttendanceToday = \App\Models\School\StaffAttendance::where('organization_id', $orgId)
            ->where('date', $today)
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // 5. Recent Activity (Specifically Exceptions: Leave + Late + Half Day + Absent)
        $recentActivity = \App\Models\School\StudentAttendance::with(['student', 'schoolClass', 'section'])
            ->where('organization_id', $orgId)
            ->whereIn('status', ['Leave', 'Late', 'Half Day', 'Absent'])
            ->latest()
            ->take(8)
            ->get();

        $todayExceptions = \App\Models\School\StudentAttendance::where('organization_id', $orgId)
            ->where('date', $today)
            ->whereIn('status', ['Leave', 'Late', 'Half Day', 'Absent'])
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // 6. Class-wise Student Strength
        $classStrength = \App\Models\School\SchoolClass::where('organization_id', $orgId)
            ->withCount([
                'sections' => function ($q) use ($activeYear) {
                    // This count is for sections, but we want students per class.
                    // We'll calculate it differently below.
                }
            ])
            ->get()
            ->map(function ($class) use ($activeYear) {
                $class->student_count = $activeYear ? \App\Models\School\SchoolEnrollment::where('class_id', $class->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->where('status', 'active')
                    ->count() : 0;
                return $class;
            });

        $stats = [
            'total_students' => $totalStudents,
            'total_staff' => $totalStaff,
            'active_classes' => $activeClassesCount,
            'attendance_percentage' => $attendancePercentage,
            'student_attendance_summary' => $studentAttendanceToday,
            'staff_attendance_summary' => $staffAttendanceToday,
            'recent_activity' => $recentActivity,
            'today_exceptions' => $todayExceptions,
            'class_strength' => $classStrength,
            'active_year' => $activeYear,
        ];

        $subscriptionService = app(\App\Services\SubscriptionService::class);
        $subscription = $subscriptionService->getSubscriptionFor($org);
        $amountDue = $subscriptionService->calculateAmountDue($subscription);
        $isSubscriptionBlocked = $subscriptionService->isBlocked($org);
        $isPaymentDue = $subscription ? now()->greaterThan($subscription->current_period_end) : false;

        $setupGuide = app(\App\Services\SetupGuideService::class)->getGuide($orgId);

        return view('school.dashboard', compact('stats', 'setupGuide', 'subscription', 'amountDue', 'isSubscriptionBlocked', 'isPaymentDue'));
    }
}
