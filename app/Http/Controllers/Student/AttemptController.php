<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use App\Services\ExamEvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttemptController extends Controller
{
    protected $evaluationService;

    public function __construct(ExamEvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    public function index()
    {
        $student = Auth::user()->student;
        $enrollment = $student ? $student->current_enrollment : null;

        if ($enrollment) {
            $exams = Exam::where('is_active', true)
                ->where('academic_year_id', $enrollment->academic_year_id)
                ->where('school_class_id', $enrollment->class_id)
                ->where(function($query) use ($enrollment) {
                    $query->whereNull('school_section_id')
                          ->orWhere('school_section_id', $enrollment->section_id);
                })
                ->latest()
                ->get();
        } else {
            $exams = collect();
        }

        $attempts = ExamAttempt::where('student_id', Auth::id())->with('exam')->latest()->get();
        return view('student.exams.index', compact('exams', 'attempts'));
    }

    public function show(Exam $exam)
    {
        $student = Auth::user()->student;
        $enrollment = $student ? $student->current_enrollment : null;

        // Verify targeting
        if (!$enrollment || 
            $exam->academic_year_id !== $enrollment->academic_year_id || 
            $exam->school_class_id !== $enrollment->class_id ||
            ($exam->school_section_id && $exam->school_section_id !== $enrollment->section_id)
        ) {
            abort(403, 'This exam is not targeted for your current class/section.');
        }

        $attemptCount = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', Auth::id())
            ->count();
        
        return view('student.exams.show', compact('exam', 'attemptCount'));
    }

    public function start(Exam $exam)
    {
        try {
            $attempt = $this->evaluationService->startAttempt($exam, Auth::id());
            return redirect()->route('student.exams.attempt', $attempt);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function attempt(ExamAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.results', $attempt);
        }

        $exam = $attempt->exam;
        $now = Carbon::now();
        $durationSeconds = $exam->duration_minutes * 60;
        $expiresAtTimestamp = $attempt->started_at->timestamp + $durationSeconds;
        
        if ($now->timestamp > $expiresAtTimestamp) {
            $this->evaluationService->autoSubmit($attempt);
            return redirect()->route('student.exams.results', $attempt);
        }

        $remainingSeconds = max(0, $expiresAtTimestamp - $now->timestamp);

        $questions = $exam->questions()
            ->with(['options' => function($query) {
                $query->inRandomOrder();
            }])
            ->inRandomOrder()
            ->get();

        return view('student.exams.attempt', compact('attempt', 'exam', 'questions', 'remainingSeconds'));
    }

    public function submit(Request $request, ExamAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id() || $attempt->status !== 'in_progress') {
            abort(403);
        }

        $answers = $request->input('answers', []); // [question_id => option_id]

        foreach ($answers as $questionId => $optionId) {
            StudentAnswer::updateOrCreate(
                ['exam_attempt_id' => $attempt->id, 'question_id' => $questionId],
                ['option_id' => $optionId]
            );
        }

        $this->evaluationService->evaluateAttempt($attempt);

        return redirect()->route('student.exams.results', $attempt);
    }

    public function results(ExamAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load('exam', 'answers.question.options', 'answers.option');
        return view('student.exams.results', compact('attempt'));
    }
}
