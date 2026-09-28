<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use Carbon\Carbon;

class ExamEvaluationService
{
    /**
     * Start a new exam attempt for a student.
     */
    public function startAttempt(Exam $exam, $studentId)
    {
        // Check if student has already reached max attempts
        $attemptCount = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->count();

        if ($attemptCount >= $exam->max_attempts) {
            throw new \Exception("You have reached the maximum number of attempts for this exam.");
        }

        // Check if current time is within exam start/end window
        $now = Carbon::now();
        if ($now->lt($exam->start_time) || $now->gt($exam->end_time)) {
            throw new \Exception("This exam is not currently active or has already ended.");
        }

        return ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $studentId,
            'started_at' => $now,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Evaluate the exam attempt and calculate the final score.
     */
    public function evaluateAttempt(ExamAttempt $attempt)
    {
        if ($attempt->status !== 'in_progress') {
            return $attempt;
        }

        $exam = $attempt->exam;
        $score = 0;
        
        $answers = $attempt->answers()->with('question', 'option')->get();

        foreach ($answers as $answer) {
            if ($answer->option && $answer->option->is_correct) {
                $score += $answer->question->marks;
                $answer->update(['is_correct' => true]);
            } else {
                $score -= $exam->negative_marks_per_wrong;
                $answer->update(['is_correct' => false]);
            }
        }

        $attempt->update([
            'score' => max(0, $score),
            'completed_at' => Carbon::now(),
            'status' => 'completed'
        ]);

        return $attempt;
    }

    /**
     * Auto-submit an attempt if valid (called by cron or middle-ware if timer expires).
     */
    public function autoSubmit(ExamAttempt $attempt)
    {
        if ($attempt->status !== 'in_progress') {
            return $attempt;
        }

        // Logic similar to evaluate but mark as auto_submitted
        $this->evaluateAttempt($attempt);
        $attempt->update(['status' => 'auto_submitted']);

        return $attempt;
    }
}
