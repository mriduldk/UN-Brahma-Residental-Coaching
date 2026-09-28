<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $query = Homework::with(['schoolClass', 'section', 'creator'])
            ->where('organization_id', $organizationId)
            ->latest();

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $homeworks = $query->paginate(15);
        $classes = SchoolClass::where('organization_id', $organizationId)->get();

        return view('school.homework.index', compact('homeworks', 'classes'));
    }

    public function create()
    {
        $organizationId = auth()->user()->currentOrganization()->id;
        $classes = SchoolClass::where('organization_id', $organizationId)->with('sections')->get();
        return view('school.homework.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'subject_name' => 'required|string|max:255',
            'type' => 'required|in:homework,assignment',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'homework_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:homework_date',
            'attachment' => 'nullable|file|max:5120', // Max 5MB
        ]);

        $data = $request->except('attachment');
        $data['organization_id'] = auth()->user()->currentOrganization()->id;
        // Get active academic year, assuming a global function or config, for now hardcode 1 or retrieve from session
        $data['academic_year_id'] = session('academic_year_id', 1); // fallback
        $data['created_by'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('homeworks', 'public');
            $data['attachment'] = $path;
        }

        Homework::create($data);

        return redirect()->route('school.homework.index')->with('success', 'Homework assigned successfully.');
    }

    public function show(Homework $homework)
    {
        // Ensure the homework belongs to the current organization
        if ($homework->organization_id != auth()->user()->currentOrganization()->id) {
            abort(403);
        }

        $homework->load(['schoolClass', 'section', 'creator', 'submissions.student']);
        return view('school.homework.show', compact('homework'));
    }

    public function edit(Homework $homework)
    {
        if ($homework->organization_id != auth()->user()->currentOrganization()->id) {
            abort(403);
        }

        $classes = SchoolClass::where('organization_id', auth()->user()->currentOrganization()->id)->with('sections')->get();
        return view('school.homework.create', compact('homework', 'classes'));
    }

    public function update(Request $request, Homework $homework)
    {
        if ($homework->organization_id != auth()->user()->currentOrganization()->id) {
            abort(403);
        }

        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'subject_name' => 'required|string|max:255',
            'type' => 'required|in:homework,assignment',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'homework_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:homework_date',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $data = $request->except('attachment');

        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($homework->attachment) {
                Storage::disk('public')->delete($homework->attachment);
            }
            $path = $request->file('attachment')->store('homeworks', 'public');
            $data['attachment'] = $path;
        }

        $homework->update($data);

        return redirect()->route('school.homework.index')->with('success', 'Homework updated successfully.');
    }

    public function destroy(Homework $homework)
    {
        if ($homework->organization_id != auth()->user()->currentOrganization()->id) {
            abort(403);
        }

        if ($homework->attachment) {
            Storage::disk('public')->delete($homework->attachment);
        }
        $homework->delete();

        return redirect()->route('school.homework.index')->with('success', 'Homework deleted successfully.');
    }

    public function showEvaluate(HomeworkSubmission $submission)
    {
        if ($submission->homework->organization_id != auth()->user()->currentOrganization()->id) {
            abort(403);
        }

        $submission->load(['homework.schoolClass', 'homework.section', 'student']);
        return view('school.homework.evaluate', compact('submission'));
    }

    public function evaluate(Request $request, HomeworkSubmission $submission)
    {
        if ($submission->homework->organization_id != auth()->user()->currentOrganization()->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,submitted,graded',
            'marks_obtained' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $submission->update([
            'status' => $request->status,
            'marks_obtained' => $request->marks_obtained,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('school.homework.show', $submission->homework_id)->with('success', 'Submission evaluated successfully.');
    }
}
