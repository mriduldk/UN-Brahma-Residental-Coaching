<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\School\SchoolTimetable;
use Illuminate\Http\Request;

class StudentTimetableController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        if (!$student) {
            abort(403, 'Student profile not found.');
        }

        $enrollment = $student->current_enrollment;
        if (!$enrollment) {
            $timetableByDay = collect();
            $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            return view('student.timetable.index', compact('timetableByDay', 'daysOfWeek'));
        }

        $timetable = SchoolTimetable::where('class_id', $enrollment->class_id)
            ->where(function ($query) use ($enrollment) {
                $query->whereNull('section_id')
                      ->orWhere('section_id', $enrollment->section_id);
            })
            ->where('academic_year_id', $enrollment->academic_year_id)
            ->where('is_active', true)
            ->with('staff')
            ->orderBy('start_time')
            ->get();

        // Group by day of week
        $timetableByDay = $timetable->groupBy('day_of_week');
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('student.timetable.index', compact('timetableByDay', 'daysOfWeek', 'enrollment'));
    }
}
