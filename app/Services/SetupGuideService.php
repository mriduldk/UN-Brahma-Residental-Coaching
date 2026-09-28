<?php

namespace App\Services;

use App\Models\SetupGuide;
use App\Models\School\AcademicYear;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolEnrollment;
use App\Models\Exam;
use App\Models\QuestionBank;
use App\Models\Question;

class SetupGuideService
{
    /**
     * Get or initialize the setup guide for an organization.
     */
    public function getGuide($orgId)
    {
        return SetupGuide::firstOrCreate(['organization_id' => $orgId]);
    }

    /**
     * Update a specific step in the setup guide.
     */
    public function updateStep($orgId, $step, $status = true)
    {
        $guide = $this->getGuide($orgId);
        $guide->update([$step => $status]);
        return $guide;
    }

    /**
     * Sync all steps for an organization by scanning the database.
     */
    public function sync($orgId)
    {
        $hasSession = AcademicYear::where('organization_id', $orgId)->where('is_active', true)->exists();
        $hasClasses = SchoolClass::where('organization_id', $orgId)->exists();
        $hasStudents = SchoolEnrollment::whereHas('schoolClass', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->exists();
        $hasBank = QuestionBank::where('organization_id', $orgId)->exists();
        $hasQuestions = Question::whereHas('bank', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->exists();
        $hasExams = Exam::where('organization_id', $orgId)->exists();
        $hasMappedQuestions = Exam::where('organization_id', $orgId)->whereHas('questions')->exists();

        $guide = $this->getGuide($orgId);
        $guide->update([
            'has_academic_session' => $hasSession,
            'has_classes' => $hasClasses,
            'has_students' => $hasStudents,
            'has_question_bank' => $hasBank,
            'has_questions' => $hasQuestions,
            'has_exams' => $hasExams,
            'has_mapped_questions' => $hasMappedQuestions,
        ]);

        return $guide;
    }
}
