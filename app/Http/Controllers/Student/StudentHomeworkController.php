<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentHomeworkController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        if (!$student) {
            abort(403, 'Student profile not found.');
        }

        $enrollment = $student->current_enrollment;
        if (!$enrollment) {
            $homeworks = collect();
            return view('student.homework.index', compact('homeworks'));
        }

        $homeworks = Homework::where('class_id', $enrollment->class_id)
            ->where(function ($query) use ($enrollment) {
                $query->whereNull('section_id')
                      ->orWhere('section_id', $enrollment->section_id);
            })
            ->where('academic_year_id', $enrollment->academic_year_id)
            ->with(['submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->latest('homework_date')
            ->paginate(15);

        return view('student.homework.index', compact('homeworks'));
    }

    public function show($id)
    {
        $student = auth()->user()->student;
        $enrollment = $student->current_enrollment;

        $homework = Homework::where('id', $id)
            ->where('class_id', $enrollment->class_id)
            ->with(['submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->firstOrFail();

        $submission = $homework->submissions->first();

        return view('student.homework.show', compact('homework', 'submission'));
    }

    public function submit(Request $request, $id)
    {
        $student = auth()->user()->student;
        $enrollment = $student->current_enrollment;

        $homework = Homework::where('id', $id)
            ->where('class_id', $enrollment->class_id)
            ->firstOrFail();

        $request->validate([
            'submission_text' => 'nullable|string',
            'attachment' => 'nullable|file|max:5120',
        ]);

        if (!$request->submission_text && !$request->hasFile('attachment')) {
            return back()->withErrors(['error' => 'Please provide text or an attachment.']);
        }

        $submission = HomeworkSubmission::firstOrNew([
            'homework_id' => $homework->id,
            'student_id' => $student->id,
        ]);

        // If already graded, cannot submit
        if ($submission->exists && $submission->status === 'graded') {
            return back()->withErrors(['error' => 'Homework is already graded and cannot be modified.']);
        }

        $submission->submission_text = $request->submission_text;
        $submission->status = 'submitted';
        $submission->submitted_at = now();

        if ($request->hasFile('attachment')) {
            if ($submission->attachment) {
                Storage::disk('public')->delete($submission->attachment);
            }
            $path = $request->file('attachment')->store('homework_submissions', 'public');
            $submission->attachment = $path;
        }

        $submission->save();

        return redirect()->route('student.homework.show', $homework->id)->with('success', 'Homework submitted successfully.');
    }
}
