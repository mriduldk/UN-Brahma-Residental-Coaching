<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\ApplicationForm;
use App\Models\Institution;
use App\Models\StudentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ApplicationFormController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $institutionIds = $user->institutions()->pluck('institutions.id');
        
        $forms = ApplicationForm::whereIn('institution_id', $institutionIds)
            ->with('institution')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('institute.forms.index', compact('forms'));
    }

    public function create()
    {
        $user = auth()->user();
        $institutions = $user->institutions()->wherePivot('role', 'admin')->get();

        if ($institutions->isEmpty()) {
            abort(403, 'You do not have permission to create forms for any institution.');
        }

        $tables = [
            'personal' => 'students',
            'address' => 'addresses',
            'academic' => 'academic_qualifications',
            'parents' => 'student_parents',
            'bank' => 'bank_details',
            'documents' => 'student_documents',
        ];

        $columns = $this->getColumnConfig($tables);

        return view('institute.forms.builder', compact('columns', 'institutions'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'application_name' => 'required|string|max:255',
            'application_description' => 'nullable|string',
            'session' => 'required|string',
            'is_application_fee_required' => 'boolean',
            'application_fee' => 'nullable|numeric|min:0',
            'is_admission_required' => 'boolean',
            'admission_fee' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'field_config' => 'nullable|array',
            'institution_id' => 'required|exists:institutions,id',
            'is_approval_required' => 'boolean',
        ]);

        // Security check: ensure the institution belongs to the user and user is admin
        if (!$user->isInstitutionAdmin($validated['institution_id'])) {
            abort(403, 'Access Denied: You must be an administrator of this institution.');
        }

        // Fetch Institution to get Organization ID
        $institution = \App\Models\Institution::findOrFail($validated['institution_id']);

        // Generate Code: ITI-25-26-001
        $parts = explode('-', $validated['session']);
        $start = substr($parts[0] ?? date('Y'), -2);
        $end = isset($parts[1]) ? substr($parts[1], -2) : substr((string)((int)$parts[0] + 1), -2);
        
        $fy = $start . '-' . $end;
        $count = ApplicationForm::where('session', $validated['session'])->count() + 1;
        $code = 'ITI-' . $fy . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $form = ApplicationForm::create([
            'application_code' => $code,
            'application_name' => $validated['application_name'],
            'application_description' => $validated['application_description'],
            'session' => $validated['session'],
            'is_application_fee_required' => $request->boolean('is_application_fee_required'),
            'application_fee' => $validated['application_fee'] ?? 0,
            'is_admission_required' => $request->boolean('is_admission_required'),
            'admission_fee' => $validated['admission_fee'] ?? 0,
            'is_approval_required' => $request->boolean('is_approval_required'),
            'status' => 'active', 
            'deadline' => $validated['deadline'],
            'created_by' => $user->id,
            'field_config' => $validated['field_config'] ?? [],
            'institution_id' => $validated['institution_id'],
            'organization_id' => $institution->organization_id,
        ]);

        return redirect()->route('institute.forms.index')->with('success', 'Form created successfully! Code: ' . $code);
    }

    public function show($id)
    {
        $user = auth()->user();
        $institutionIds = $user->institutions()->pluck('institutions.id');
        
        $form = ApplicationForm::whereIn('institution_id', $institutionIds)->findOrFail($id);
        
        return view('institute.forms.show', compact('form'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $institutions = $user->institutions()->get();
        $institutionIds = $institutions->pluck('id');
        
        $form = ApplicationForm::whereIn('institution_id', $institutionIds)->findOrFail($id);

        if (!$user->isInstitutionAdmin($form->institution_id)) {
            abort(403, 'Access Denied: You must be an administrator of this institution.');
        }

        $tables = [
            'personal' => 'students',
            'address' => 'addresses',
            'academic' => 'academic_qualifications',
            'parents' => 'student_parents',
            'bank' => 'bank_details',
            'documents' => 'student_documents',
        ];

        $columns = $this->getColumnConfig($tables);

        return view('institute.forms.builder', compact('columns', 'form', 'institutions'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $institutionIds = $user->institutions()->pluck('institutions.id');
        $form = ApplicationForm::whereIn('institution_id', $institutionIds)->findOrFail($id);

        $validated = $request->validate([
            'application_name' => 'required|string|max:255',
            'application_description' => 'nullable|string',
            'session' => 'required|string',
            'is_application_fee_required' => 'boolean',
            'application_fee' => 'nullable|numeric|min:0',
            'is_admission_required' => 'boolean',
            'admission_fee' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'field_config' => 'nullable|array',
            'status' => 'required|in:active,draft,closed',
            'institution_id' => 'required|exists:institutions,id',
            'is_approval_required' => 'boolean',
        ]);

        // Security check: ensure the new institution belongs to the user and user is admin
        if (!$user->isInstitutionAdmin($validated['institution_id'])) {
            abort(403, 'Access Denied: You must be an administrator of this institution.');
        }

        $form->update([
            'application_name' => $validated['application_name'],
            'application_description' => $validated['application_description'],
            'session' => $validated['session'],
            'is_application_fee_required' => $request->boolean('is_application_fee_required'),
            'application_fee' => $validated['application_fee'] ?? 0,
            'is_admission_required' => $request->boolean('is_admission_required'),
            'admission_fee' => $validated['admission_fee'] ?? 0,
            'is_approval_required' => $request->boolean('is_approval_required'),
            'deadline' => $validated['deadline'],
            'status' => $validated['status'],
            'field_config' => $validated['field_config'] ?? [],
            'institution_id' => $validated['institution_id'],
        ]);

        return redirect()->route('institute.forms.index')->with('success', 'Form updated successfully!');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $institutionIds = $user->institutions()->pluck('institutions.id');
        $form = ApplicationForm::whereIn('institution_id', $institutionIds)->findOrFail($id);

        if (!$user->isInstitutionAdmin($form->institution_id)) {
            abort(403, 'Access Denied: You must be an administrator of this institution.');
        }

        $form->delete();
        return redirect()->route('institute.forms.index')->with('success', 'Form deleted successfully!');
    }

    public function applications(Request $request)
    {
        $user = auth()->user();
        $institutionIds = $user->institutions()->pluck('institutions.id');
        
        $forms = ApplicationForm::whereIn('institution_id', $institutionIds)->latest()->get();
        $selectedForm = null;
        
        if ($request->has('form_id')) {
            $selectedForm = ApplicationForm::whereIn('institution_id', $institutionIds)->findOrFail($request->form_id);
        }

        $query = StudentApplication::with(['application_form', 'application_student'])
            ->whereIn('institution_id', $institutionIds);

        if ($selectedForm) {
            $query->where('application_form_id', $selectedForm->id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_code', 'like', "%{$search}%")
                  ->orWhereHas('application_student', function($qs) use ($search) {
                      $qs->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%");
                  });
            });
        }

        $applications = $query->latest()->paginate(10);

        return view('institute.forms.applications', compact('applications', 'forms', 'selectedForm'));
    }

    public function applicationDetail($id)
    {
        $user = auth()->user();
        $institutionIds = $user->institutions()->pluck('institutions.id');

        $application = StudentApplication::with([
            'application_form',
            'application_student',
            'application_address',
            'application_parent',
            'application_bank_detail',
            'application_academic_qualifications',
            'application_documents'
        ])->whereIn('institution_id', $institutionIds)->findOrFail($id);

        return view('institute.forms.application_detail', compact('application'));
    }

    public function updateApplicationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $user = auth()->user();
        $institutionIds = $user->institutions()->pluck('institutions.id');

        $application = StudentApplication::whereIn('institution_id', $institutionIds)->findOrFail($id);

        if (!$user->isInstitutionAdmin($application->institution_id)) {
            abort(403, 'Access Denied: You must be an administrator of this institution.');
        }

        $application->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Application status updated to ' . ucfirst($request->status));
    }

    public function updateApprovalStatus(Request $request, $id)
    {
        $request->validate([
            'approval_status' => 'required|in:pending,approved,rejected',
            'approval_remarks' => 'nullable|string',
        ]);

        $user = auth()->user();
        $institutionIds = $user->institutions()->pluck('institutions.id');

        $application = StudentApplication::whereIn('institution_id', $institutionIds)->findOrFail($id);

        if (!$user->isInstitutionAdmin($application->institution_id)) {
            abort(403, 'Access Denied: You must be an administrator of this institution.');
        }

        $application->update([
            'approval_status' => $request->approval_status,
            'approval_remarks' => $request->approval_remarks,
        ]);

        return redirect()->back()->with('success', 'Application approval status updated to ' . ucfirst($request->approval_status));
    }

    private function getColumnConfig($tables)
    {
        $columns = [];
        foreach ($tables as $key => $table) {
            $cols = Schema::getColumnListing($table);
            $cols = array_diff($cols, ['id', 'user_id', 'created_at', 'updated_at', 'student_id', 'email_verified_at', 'password', 'remember_token', 'role', 'same_as_permanent']);
            
            if ($key === 'personal') {
                $priority = ['full_name', 'phone_number', 'email', 'gender', 'dob', 'category', 'age'];
                $columns[$key] = $this->sortColumns($cols, $priority);
            } elseif ($key === 'academic') {
                $priority = ['examination', 'board_university', 'passing_year', 'percentage_grade', 'division'];
                $columns[$key] = $this->sortColumns($cols, $priority);
            } elseif ($key === 'address') {
                 $grouped = [
                     'Permanent Address' => [],
                     'Present Address' => [],
                 ];
                 foreach ($cols as $col) {
                     if (str_starts_with($col, 'permanent_')) $grouped['Permanent Address'][] = $col;
                     elseif (str_starts_with($col, 'present_')) $grouped['Present Address'][] = $col;
                     else $grouped['Other'][] = $col;
                 }
                 $columns[$key] = array_filter($grouped);
            } elseif ($key === 'parents') {
                 $grouped = [
                     'Father\'s Details' => [],
                     'Mother\'s Details' => [],
                     'Guardian\'s Details' => [],
                 ];
                 foreach ($cols as $col) {
                     if (str_starts_with($col, 'father_')) $grouped['Father\'s Details'][] = $col;
                     elseif (str_starts_with($col, 'mother_')) $grouped['Mother\'s Details'][] = $col;
                     elseif (str_starts_with($col, 'guardian_')) $grouped['Guardian\'s Details'][] = $col;
                     else $grouped['Other'][] = $col;
                 }
                 $columns[$key] = array_filter($grouped);
            } else {
                $columns[$key] = array_values($cols);
            }
        }
        return $columns;
    }

    private function sortColumns($cols, $priority) {
        $cols = array_values($cols);
        usort($cols, function ($a, $b) use ($priority) {
            $posA = array_search($a, $priority);
            $posB = array_search($b, $priority);
            
            if ($posA === false && $posB === false) return 0;
            if ($posA === false) return 1;
            if ($posB === false) return -1;
            
            return $posA - $posB;
        });
        return $cols;
    }
}
