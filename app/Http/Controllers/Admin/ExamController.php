<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\School\AcademicYear;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolSection;
use App\Http\Requests\StoreExamRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orgId = Auth::user()->currentOrganization()->id;
        $exams = Exam::where('organization_id', $orgId)
            ->with(['creator', 'questions'])
            ->latest()
            ->paginate(10);
        
        $setupGuide = app(\App\Services\SetupGuideService::class)->getGuide($orgId);
            
        return view('school.exams.index', compact('exams', 'setupGuide'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orgId = Auth::user()->currentOrganization()->id;
        $academicYears = AcademicYear::where('organization_id', $orgId)->where('is_active', true)->get();
        $classes = SchoolClass::where('organization_id', $orgId)->with('sections')->get();
        return view('school.exams.create', compact('academicYears', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::id();
        $validated['organization_id'] = Auth::user()->currentOrganization()->id;

        Exam::create($validated);

        app(\App\Services\SetupGuideService::class)->updateStep($validated['organization_id'], 'has_exams', true);

        return redirect()->route('administration.exams.index')->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $this->authorizeOrganization($exam);
        
        $exam->load('questions.options');
        $banks = \App\Models\QuestionBank::where('organization_id', Auth::user()->currentOrganization()->id)->latest()->get();
        return view('school.exams.show', compact('exam', 'banks'));
    }

    /**
     * Link questions from a bank to an exam.
     */
    public function linkQuestions(Request $request, Exam $exam)
    {
        $this->authorizeOrganization($exam);

        $request->validate([
            'question_ids' => 'nullable|array',
            'question_bank_id' => 'required|exists:question_banks,id'
        ]);

        $bank = \App\Models\QuestionBank::findOrFail($request->question_bank_id);
        if ($bank->organization_id !== Auth::user()->currentOrganization()->id) {
            abort(403, 'Unauthorized access to question bank.');
        }

        $exam->update(['question_bank_id' => $request->question_bank_id]);

        $questionIds = $request->question_ids;
        
        if (empty($questionIds)) {
            // Map all questions from the bank
            $questionIds = $bank->questions()->pluck('id')->toArray();
        }
        
        if (!empty($questionIds)) {
            $exam->questions()->syncWithoutDetaching($questionIds);
            app(\App\Services\SetupGuideService::class)->updateStep(Auth::user()->currentOrganization()->id, 'has_mapped_questions', true);
        }

        return redirect()->back()->with('success', 'Questions mapped successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        $this->authorizeOrganization($exam);

        $orgId = Auth::user()->currentOrganization()->id;
        $academicYears = AcademicYear::where('organization_id', $orgId)->where('is_active', true)->get();
        $classes = SchoolClass::where('organization_id', $orgId)->with('sections')->get();
        return view('school.exams.edit', compact('exam', 'academicYears', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreExamRequest $request, Exam $exam)
    {
        $this->authorizeOrganization($exam);

        $validated = $request->validated();
        $exam->update($validated);

        return redirect()->route('administration.exams.index')->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $this->authorizeOrganization($exam);

        $exam->delete();
        return redirect()->route('administration.exams.index')->with('success', 'Exam deleted successfully.');
    }

    /**
     * Display the results of the specified exam.
     */
    public function results(Exam $exam)
    {
        $this->authorizeOrganization($exam);

        $attempts = $exam->attempts()->with('student')->latest()->paginate(20);
        return view('school.exams.results', compact('exam', 'attempts'));
    }

    /**
     * Display the specified attempt detail.
     */
    public function showAttempt(ExamAttempt $attempt)
    {
        $attempt->load(['exam', 'student', 'answers.question.options', 'answers.option']);
        
        if ($attempt->exam->organization_id !== Auth::user()->currentOrganization()->id) {
            abort(403, 'Unauthorized access to exam attempt.');
        }

        return view('school.exams.attempt_detail', compact('attempt'));
    }

    /**
     * Authorize that the model belongs to the current user's organization.
     */
    protected function authorizeOrganization($model)
    {
        $orgId = Auth::user()->currentOrganization()->id;
        if ($model->organization_id !== $orgId) {
            abort(403, 'Unauthorized access to this resource.');
        }
    }
}
