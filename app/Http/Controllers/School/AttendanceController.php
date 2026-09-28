<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School\SchoolStaff;
use App\Models\School\StaffAttendance;
use App\Models\School\AcademicYear;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolEnrollment;
use App\Models\School\StudentAttendance;
use App\Models\School\SchoolSection;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('school.attendance.index');
    }

    public function staffIndex(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        $date = $request->input('date', date('Y-m-d'));
        
        $staffs = SchoolStaff::where('organization_id', $organizationId)
                            ->where('is_active', true)
                            ->orderBy('department')
                            ->orderBy('full_name')
                            ->get();
                            
        $attendances = StaffAttendance::where('organization_id', $organizationId)
                            ->where('date', $date)
                            ->get()
                            ->keyBy('school_staff_id');
                            
        return view('school.attendance.staff', compact('staffs', 'attendances', 'date'));
    }

    public function staffStore(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:Present,Absent,Half Day,Late,Leave,Holiday',
            'attendance.*.remarks' => 'nullable|string|max:255',
        ]);
        
        $organizationId = auth()->user()->currentOrganization()->id;
        $date = $request->date;
        
        foreach ($request->attendance as $staffId => $data) {
            StaffAttendance::updateOrCreate(
                [
                    'organization_id' => $organizationId,
                    'school_staff_id' => $staffId,
                    'date' => $date,
                ],
                [
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null,
                ]
            );
        }
        
        return redirect()->route('school.attendance.staff', ['date' => $date])
                         ->with('success', 'Staff attendance saved successfully.');
    }

    public function studentIndex(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        $academicYears = AcademicYear::where('organization_id', $organizationId)->latest()->get();
        $classes = SchoolClass::where('organization_id', $organizationId)->with('sections')->get();
        
        $date = $request->input('date', date('Y-m-d'));
        $academicYearId = $request->input('academic_year_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        
        $students = [];
        $attendances = [];
        
        if ($academicYearId && $classId) {
            $query = SchoolEnrollment::with('student')
                        ->where('academic_year_id', $academicYearId)
                        ->where('class_id', $classId)
                        ->where('status', 'Active');
                        
            if ($sectionId) {
                $query->where('section_id', $sectionId);
            }
            
            $enrollments = $query->get();
            $students = $enrollments->pluck('student');
            
            $attendances = StudentAttendance::where('organization_id', $organizationId)
                                ->where('academic_year_id', $academicYearId)
                                ->where('class_id', $classId)
                                ->when($sectionId, function($q) use ($sectionId) {
                                    return $q->where('section_id', $sectionId);
                                })
                                ->where('date', $date)
                                ->get()
                                ->keyBy('student_id');
        }
        
        return view('school.attendance.students', compact('academicYears', 'classes', 'students', 'attendances', 'date', 'academicYearId', 'classId', 'sectionId'));
    }

    public function studentStore(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'academic_year_id' => 'required|exists:school_academic_years,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:Present,Absent,Half Day,Late,Leave,Holiday',
            'attendance.*.remarks' => 'nullable|string|max:255',
        ]);
        
        $organizationId = auth()->user()->currentOrganization()->id;
        $date = $request->date;
        $academicYearId = $request->academic_year_id;
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        
        // Use a transaction or simply upsert
        foreach ($request->attendance as $studentId => $data) {
            StudentAttendance::updateOrCreate(
                [
                    'organization_id' => $organizationId,
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'academic_year_id' => $academicYearId,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null,
                ]
            );
        }
        
        return redirect()->route('school.attendance.students', [
            'date' => $date,
            'academic_year_id' => $academicYearId,
            'class_id' => $classId,
            'section_id' => $sectionId,
        ])->with('success', 'Student attendance saved successfully.');
    }

    public function staffReport(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        
        $staffs = SchoolStaff::where('organization_id', $organizationId)
                            ->where('is_active', true)
                            ->orderBy('department')
                            ->orderBy('full_name')
                            ->get();
                            
        $attendances = StaffAttendance::where('organization_id', $organizationId)
                            ->whereYear('date', $year)
                            ->whereMonth('date', $month)
                            ->get()
                            ->groupBy('school_staff_id');
                            
        return view('school.attendance.report-staff', compact('staffs', 'attendances', 'month', 'year', 'daysInMonth'));
    }

    public function studentReport(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        $academicYears = AcademicYear::where('organization_id', $organizationId)->latest()->get();
        $classes = SchoolClass::where('organization_id', $organizationId)->with('sections')->get();
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $academicYearId = $request->input('academic_year_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        
        $students = collect();
        $attendances = collect();
        
        if ($academicYearId && $classId) {
            $query = SchoolEnrollment::with('student')
                        ->where('academic_year_id', $academicYearId)
                        ->where('class_id', $classId)
                        ->where('status', 'Active');
                        
            if ($sectionId) {
                $query->where('section_id', $sectionId);
            }
            
            $enrollments = $query->get();
            $students = $enrollments->pluck('student')->sortBy(function($s) {
                return $s->user->name ?? '';
            });
            
            $attendances = StudentAttendance::where('organization_id', $organizationId)
                                ->where('academic_year_id', $academicYearId)
                                ->where('class_id', $classId)
                                ->when($sectionId, function($q) use ($sectionId) {
                                    return $q->where('section_id', $sectionId);
                                })
                                ->whereYear('date', $year)
                                ->whereMonth('date', $month)
                                ->get()
                                ->groupBy('student_id');
        }
        
        return view('school.attendance.report-students', compact('academicYears', 'classes', 'students', 'attendances', 'month', 'year', 'daysInMonth', 'academicYearId', 'classId', 'sectionId'));
    }

    public function studentPercentageReport(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        $academicYears = AcademicYear::where('organization_id', $organizationId)->latest()->get();
        $classes = SchoolClass::where('organization_id', $organizationId)->with('sections')->get();
        
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        
        $academicYearId = $request->input('academic_year_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        
        $reportData = collect();
        
        if ($academicYearId && $classId) {
            $enrollmentsQuery = SchoolEnrollment::with('student.user')
                                ->where('academic_year_id', $academicYearId)
                                ->where('class_id', $classId)
                                ->where('status', 'Active');
            
            if ($sectionId) {
                $enrollmentsQuery->where('section_id', $sectionId);
            }
            
            $enrollments = $enrollmentsQuery->get();
            $studentIds = $enrollments->pluck('student_id');
            
            $attendances = StudentAttendance::where('organization_id', $organizationId)
                                    ->whereIn('student_id', $studentIds)
                                    ->whereBetween('date', [$dateFrom, $dateTo])
                                    ->get()
                                    ->groupBy('student_id');
            
            foreach ($enrollments as $enrollment) {
                $student = $enrollment->student;
                $studentAttendances = $attendances->get($student->id, collect());
                
                $totalDays = $studentAttendances->where('status', '!=', 'Holiday')->count();
                $presentDays = $studentAttendances->where('status', 'Present')->count();
                $absentDays = $studentAttendances->where('status', 'Absent')->count();
                $halfDays = $studentAttendances->where('status', 'Half Day')->count();
                $lateDays = $studentAttendances->where('status', 'Late')->count();
                $leaveDays = $studentAttendances->where('status', 'Leave')->count();
                $holidayDays = $studentAttendances->where('status', 'Holiday')->count();
                
                $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;
                
                $reportData->push([
                    'student' => $student,
                    'total_days' => $totalDays,
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'half_days' => $halfDays,
                    'late_days' => $lateDays,
                    'leave_days' => $leaveDays,
                    'holiday_days' => $holidayDays,
                    'percentage' => $percentage,
                    'registration_number' => $student->registration_number ?? '-'
                ]);
            }
            
            $reportData = $reportData->sortByDesc('percentage');
        }
        
        return view('school.attendance.percentage', compact(
            'academicYears', 'classes', 'reportData', 
            'dateFrom', 'dateTo', 'academicYearId', 'classId', 'sectionId'
        ));
    }

    public function studentPercentageReportExport(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        $academicYearId = $request->input('academic_year_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        
        if (!$academicYearId || !$classId) {
            return back()->with('error', 'Select Session and Class to export.');
        }

        $schoolClass = SchoolClass::find($classId);

        $enrollmentsQuery = SchoolEnrollment::with('student.user')
                            ->where('academic_year_id', $academicYearId)
                            ->where('class_id', $classId)
                            ->where('status', 'Active');
        
        if ($sectionId) {
            $enrollmentsQuery->where('section_id', $sectionId);
        }
        
        $enrollments = $enrollmentsQuery->get();
        $studentIds = $enrollments->pluck('student_id');
        
        $attendances = StudentAttendance::where('organization_id', $organizationId)
                                ->whereIn('student_id', $studentIds)
                                ->whereBetween('date', [$dateFrom, $dateTo])
                                ->get()
                                ->groupBy('student_id');

        $fileName = 'Attendance_Percentage_' . str_replace(' ', '_', $schoolClass->name ?? 'Report') . '_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($enrollments, $attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Sl No', 'Student Name', 'Registration No', 'Working Days', 'Present', 'Absent', 'Percentage (%)']);

            $i = 1;
            foreach ($enrollments as $enrollment) {
                $student = $enrollment->student;
                $studentAttendances = $attendances->get($student->id, collect());
                
                $totalDays = $studentAttendances->where('status', '!=', 'Holiday')->count();
                $presentDays = $studentAttendances->where('status', 'Present')->count();
                $absentDays = $studentAttendances->where('status', 'Absent')->count();
                $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

                fputcsv($file, [
                    $i++,
                    $student->user->name ?? 'N/A',
                    $student->registration_number ?? '-',
                    $totalDays,
                    $presentDays,
                    $absentDays,
                    $percentage . '%'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function studentReportExport(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $academicYearId = $request->input('academic_year_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        
        if (!$academicYearId || !$classId) {
            return back()->with('error', 'Select Session and Class to export.');
        }

        $schoolClass = SchoolClass::find($classId);
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        $query = SchoolEnrollment::with('student.user')
                    ->where('academic_year_id', $academicYearId)
                    ->where('class_id', $classId)
                    ->where('status', 'Active');
                    
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
        
        $enrollments = $query->get();
        $studentIds = $enrollments->pluck('student_id');
        
        $attendances = StudentAttendance::where('organization_id', $organizationId)
                            ->whereIn('student_id', $studentIds)
                            ->whereYear('date', $year)
                            ->whereMonth('date', $month)
                            ->get()
                            ->groupBy('student_id');

        $fileName = 'Monthly_Attendance_' . date('M_Y', mktime(0, 0, 0, $month, 1, $year)) . '_' . str_replace(' ', '_', $schoolClass->name ?? 'Report') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($enrollments, $attendances, $daysInMonth, $month, $year) {
            $file = fopen('php://output', 'w');
            
            // Header Row
            $header = ['Sl No', 'Student Name', 'Registration No'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $header[] = $d;
            }
            array_push($header, 'P', 'A', 'H', 'L', 'V', 'O');
            fputcsv($file, $header);

            $i = 1;
            foreach ($enrollments as $enrollment) {
                $student = $enrollment->student;
                $studentAttendances = $attendances->get($student->id, collect())->keyBy(function($item) {
                    return Carbon::parse($item->date)->day;
                });

                $row = [
                    $i++,
                    $student->user->name ?? 'N/A',
                    $student->registration_number ?? '-'
                ];

                $p = 0; $a = 0; $h = 0; $l = 0; $v = 0; $o = 0;

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $att = $studentAttendances->get($d);
                    $status = '-';
                    if ($att) {
                        switch ($att->status) {
                            case 'Present': $status = 'P'; $p++; break;
                            case 'Absent': $status = 'A'; $a++; break;
                            case 'Half Day': $status = 'H'; $h++; break;
                            case 'Late': $status = 'L'; $l++; break;
                            case 'Leave': $status = 'V'; $v++; break;
                            case 'Holiday': $status = 'O'; $o++; break;
                        }
                    }
                    $row[] = $status;
                }

                array_push($row, $p, $a, $h, $l, $v, $o);
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function staffReportExport(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        
        $staffs = SchoolStaff::where('organization_id', $organizationId)
                            ->where('is_active', true)
                            ->orderBy('department')
                            ->orderBy('full_name')
                            ->get();
                            
        $attendances = StaffAttendance::where('organization_id', $organizationId)
                            ->whereYear('date', $year)
                            ->whereMonth('date', $month)
                            ->get()
                            ->groupBy('school_staff_id');

        $fileName = 'Staff_Monthly_Attendance_' . date('M_Y', mktime(0, 0, 0, $month, 1, $year)) . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($staffs, $attendances, $daysInMonth) {
            $file = fopen('php://output', 'w');
            
            // Header Row
            $header = ['Sl No', 'Staff Name', 'Department', 'Employee ID'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $header[] = $d;
            }
            array_push($header, 'P', 'A', 'H', 'L', 'V', 'O');
            fputcsv($file, $header);

            $i = 1;
            foreach ($staffs as $staff) {
                $staffAttendances = $attendances->get($staff->id, collect())->keyBy(function($item) {
                    return Carbon::parse($item->date)->day;
                });

                $row = [
                    $i++,
                    $staff->full_name,
                    $staff->department ?? '-',
                    $staff->employee_id ?? '-'
                ];

                $p = 0; $a = 0; $h = 0; $l = 0; $v = 0; $o = 0;

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $att = $staffAttendances->get($d);
                    $status = '-';
                    if ($att) {
                        switch ($att->status) {
                            case 'Present': $status = 'P'; $p++; break;
                            case 'Absent': $status = 'A'; $a++; break;
                            case 'Half Day': $status = 'H'; $h++; break;
                            case 'Late': $status = 'L'; $l++; break;
                            case 'Leave': $status = 'V'; $v++; break;
                            case 'Holiday': $status = 'O'; $o++; break;
                        }
                    }
                    $row[] = $status;
                }

                array_push($row, $p, $a, $h, $l, $v, $o);
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
