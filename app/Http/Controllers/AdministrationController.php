<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\ApplicationForm;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdministrationController extends Controller
{
    public function onboarding(Request $request)
    {
        if (auth()->user()->organizations()->exists()) {
             return redirect()->route('administration.dashboard');
        }

        // If business type is not in session, show selection page
        if (!session()->has('onboarding_business_type')) {
            return view('administration.business_type');
        }

        return view('administration.onboarding');
    }

    public function selectBusinessType(Request $request)
    {
        $request->validate([
            'business_type' => 'required|in:Tuition,School,Coaching'
        ]);

        session(['onboarding_business_type' => $request->business_type]);

        return redirect()->route('administration.onboarding');
    }

    public function storeOnboarding(Request $request)
    {
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'type' => 'required|string',
            'industry' => 'nullable|string',
            'registration_no' => 'nullable|string',
            'gst_no' => 'nullable|string',
            'pan_no' => 'nullable|string',
            'tan_no' => 'nullable|string',
            'website' => 'nullable|url',
        ]);

        $user = auth()->user();
        
        $shortName = $request->short_name ?? \Illuminate\Support\Str::limit($request->organization_name, 50, '');

        $org = \App\Models\Organization::create([
            'user_id' => $user->id,
            'organization_name' => $request->organization_name,
            'short_name' => $shortName,
            'type' => $request->type,
            'business_type' => session('onboarding_business_type', 'School'), // Store selected business type
            'category' => $request->category,
            'industry' => $request->industry,
            'registration_no' => $request->registration_no,
            'gst_no' => $request->gst_no,
            'pan_no' => $request->pan_no,
            'tan_no' => $request->tan_no,
            'website' => $request->website,
            'logo' => null, // File upload handled later if needed
        ]);

        // Attach the user as admin
        $user->organizations()->attach($org->id, ['role' => 'admin']);
        
        // Create default institution
        \App\Models\Institution::create([
            'organization_id' => $org->id,
            'institution_name' => $org->organization_name . ' Institution',
            'institution_code' => 'INST-' . time() . '-' . rand(100, 999),
            'contact_number' => $user->mobile_number,
            'email' => $user->email,
        ]);

        return redirect()->route('school.dashboard')->with('success', 'Organization setup complete!');
    }

    public function dashboard()
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) {
             return view('administration.dashboard', [
                'totalApplications' => 0,
                'pendingCount' => 0,
                'totalRevenue' => 0,
                'admissionRate' => 0,
                'recentTransactions' => collect([]),
                'tradeStats' => collect([]),
                'totalPendingEmi' => 0,
                'upcomingDuesCount' => 0,
                'unsettledCash' => 0,
                 'institutions' => collect([]),
                'enrolledStudentsCount' => 0,
                'admRevenue' => 0,
                'overallPendingBalance' => 0,
            ]);
        }

        $institutions = $org->institutions()->withCount(['application_forms'])->get();
        $institutionIds = $institutions->pluck('id');

        // Total Applications
        $totalApplications = \App\Models\StudentApplication::whereHas('application_form', function($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->count();

        // Pending Review (status = 'submitted')
        $pendingCount = \App\Models\StudentApplication::whereHas('application_form', function($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'submitted')->count();

        // Total Revenue (Successful transactions)
        $regRevenue = \App\Models\PaymentTransaction::where('status', 'captured')
            ->whereHas('application.application_form', function($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            })->sum('amount');

        $admRevenue = \App\Models\AdmissionPaymentTransaction::where('status', 'captured')
            ->whereHas('order.student_application.application_form', function($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            })->sum('amount');

        $totalRevenue = $regRevenue + $admRevenue;

        // Admission Rate: Approved / Total
        $approvedCount = \App\Models\StudentApplication::whereHas('application_form', function($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'approved')->count();

        $admissionRate = $totalApplications > 0 ? round(($approvedCount / $totalApplications) * 100) : 0;

        // EMI and Cash Metrics (Organization Wide)
        $admissionsData = \App\Models\AdmissionStudent::whereIn('institution_id', $institutionIds)->get();
        
        $totalPendingEmi = 0;
        $overallPendingBalance = 0;
        $upcomingDuesCount = 0;
        $unsettledCash = 0;
        $nextWeek = now()->addDays(7);
        $enrolledStudentsCount = $admissionsData->count();

        foreach ($admissionsData as $adm) {
            if ($adm->payment_type === 'emi') {
                $totalPendingEmi += $adm->pending_amount;
                
                if ($adm->next_payment_date && $adm->pending_amount > 0) {
                    $dueDate = \Carbon\Carbon::parse($adm->next_payment_date);
                    if ($dueDate->isPast() || $dueDate->lessThanOrEqualTo($nextWeek)) {
                        $upcomingDuesCount++;
                    }
                }
            }

            $overallPendingBalance += $adm->pending_amount;

            // Unsettled cash
            $unsettledCash += \App\Models\AdmissionPayment::where('student_application_id', $adm->student_application_id)
                ->where('payment_type', 'cash')
                ->where('is_settled', false)
                ->sum('paid_amount');
        }

        // Recent Transactions (latest 5 successful)
        $regTransactions = \App\Models\PaymentTransaction::with(['application.student', 'application.application_form'])
            ->where('status', 'captured')
            ->whereHas('application.application_form', function($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($t) {
                $t->payment_type = 'Registration';
                $t->student_name = $t->application->student->full_name ?? 'N/A';
                $t->trade = $t->application->application_form->application_name ?? 'N/A';
                return $t;
            });

        $admTransactions = \App\Models\AdmissionPaymentTransaction::with(['order.student_application.student', 'order.student_application.application_form'])
            ->where('status', 'captured')
            ->whereHas('order.student_application.application_form', function($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($t) {
                $t->payment_type = 'Admission';
                $t->student_name = $t->order->student_application->student->full_name ?? 'N/A';
                $t->trade = $t->order->student_application->application_form->application_name ?? 'N/A';
                return $t;
            });

        $recentTransactions = $regTransactions->concat($admTransactions)->sortByDesc('created_at')->take(5);

        // Trade Stats
        $tradeStats = \App\Models\StudentApplication::whereHas('application_form', function($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })
        ->join('application_forms', 'student_applications.application_form_id', '=', 'application_forms.id')
        ->select('application_forms.application_name as trade', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
        ->groupBy('application_forms.application_name')
        ->get();

        return view('administration.dashboard', compact(
            'totalApplications', 
            'pendingCount', 
            'totalRevenue', 
            'admissionRate', 
            'recentTransactions', 
            'tradeStats',
            'totalPendingEmi',
            'overallPendingBalance',
            'admRevenue',
            'enrolledStudentsCount',
            'upcomingDuesCount',
            'unsettledCash',
            'institutions'
        ));
    }

    public function forms()
    {
        // $forms = \App\Models\ApplicationForm::where('created_by', auth()->id())->get(); // OLD logic
        $org = auth()->user()->currentOrganization();
        $forms = $org ? $org->applicationForms()->with('institution')->orderBy('created_at', 'desc')->get() : collect([]);

        return view('administration.forms.index', compact('forms', 'org'));
    }

    public function formBuilder()
    {
        $org = auth()->user()->currentOrganization();
        
        if (!$org || !auth()->user()->isAdmin($org->id)) {
             abort(403, 'Only Admins can create forms.');
        }

        $institutions = $org->institutions;
        $globalConfig = \App\Models\GlobalConfig::first();
        $canCreate = $org->canCreateForm();
        $quotaPrice = $globalConfig->form_creation_charge;
        $registrationFeeCharge = $globalConfig->registration_fee_charge;

        $tables = [
            'personal' => 'students',
            'address' => 'addresses',
            'academic' => 'academic_qualifications',
            'parents' => 'student_parents',
            'bank' => 'bank_details',
            'documents' => 'student_documents',
        ];

        $columns = [];
        foreach ($tables as $key => $table) {
            $cols = \Illuminate\Support\Facades\Schema::getColumnListing($table);
            $cols = array_diff($cols, [
                'id', 'user_id', 'created_at', 'updated_at', 'student_id', 'email_verified_at', 'password', 'remember_token', 'role', 'same_as_permanent',
                'marital_status', 'is_ex_serviceman', 'ncc_certificate', 'has_national_sports_achievement', 'has_fine_arts_cultural_achievement',
                'passport', 'transfer_certificate', 'bed_parangat_marksheet', 'bed_parangat_certificate', 'tet_certificate', 'pwd_certificate',
                'ex_serviceman_certificate', 'ncc_certificate_doc', 'sports_certificate', 'cultural_certificate', 'language_diploma_certificate'
            ]);
            
            // Custom Logic per Section
            if ($key === 'personal') {
                $priority = ['full_name', 'phone_number', 'email', 'gender', 'dob', 'category', 'age'];
                $cols = $this->sortColumns($cols, $priority);
                $columns[$key] = $cols;
            } elseif ($key === 'academic') {
                $priority = ['examination', 'board_university', 'passing_year', 'percentage_grade', 'division'];
                $cols = $this->sortColumns($cols, $priority);
                $columns[$key] = $cols;
            } elseif ($key === 'address') {
                 // Split into groups
                 $grouped = [
                     'Permanent Address' => [],
                     'Present Address' => [],
                 ];
                 foreach ($cols as $col) {
                     if (str_starts_with($col, 'permanent_')) $grouped['Permanent Address'][] = $col;
                     elseif (str_starts_with($col, 'present_')) $grouped['Present Address'][] = $col;
                     else $grouped['Other'][] = $col; // Fallback
                 }
                 // Remove empty groups (like 'Other')
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

        $orgConfig = $org->configuration ?? new \App\Models\OrganizationConfiguration();

        return view('administration.forms.builder', compact('columns', 'institutions', 'canCreate', 'quotaPrice', 'registrationFeeCharge', 'org', 'orgConfig'));
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

    public function storeForm(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Unlock full access? Ask your admin for the keys!');
        }

        $validated = $request->validate([
            'application_name' => 'required|string|max:255',
            'application_description' => 'required|string',
            'session' => 'required|string',
            'is_application_fee_required' => 'boolean',
            'application_fee' => $request->boolean('is_application_fee_required') ? 'required|numeric|min:1' : 'nullable|numeric|min:0',
            'is_admission_required' => 'boolean',
            'admission_fee' => $request->boolean('is_admission_required') ? 'required|numeric|min:1' : 'nullable|numeric|min:0',
            'deadline' => 'required|date',
            'institution_id' => 'required|exists:institutions,id',
            'field_config' => 'nullable|array',
            'is_approval_required' => 'boolean',
            'advertisement_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'no_of_vacancies' => 'nullable|integer|min:1',
        ]);

        $data = $request->all();
        if ($request->hasFile('advertisement_pdf')) {
            $path = $request->file('advertisement_pdf')->store('advertisements', 'public');
            $data['advertisement_pdf_path'] = $path;
        }

        $res = $this->performFormCreation($data, $org, auth()->user());

        if (isset($res['error'])) {
            return back()->withInput()->with('error', $res['error']);
        }

        return redirect()->route('administration.forms')->with('success', 'Form created successfully! Code: ' . $res['form']->application_code);
    }

    private function performFormCreation(array $data, \App\Models\Organization $org, \App\Models\User $user)
    {
        // Enforce Feature Flags
        $orgConfig = $org->configuration;
        $isAppFeeRequired = filter_var($data['is_application_fee_required'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $isAdmRequired = filter_var($data['is_admission_required'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($isAppFeeRequired && ($orgConfig && !$orgConfig->is_registration_fee_enabled)) {
            return ['error' => 'Registration Fee is currently disabled for your organization.'];
        }

        if ($isAdmRequired && ($orgConfig && !$orgConfig->is_admission_enabled)) {
            return ['error' => 'Admission is currently disabled for your organization.'];
        }

        // Generate Code
        $parts = explode('-', $data['session']);
        $start = substr($parts[0] ?? date('Y'), -2);
        $end = isset($parts[1]) ? substr($parts[1], -2) : substr((string)((int)$parts[0] + 1), -2);
        $fy = $start . '-' . $end;
        $count = \App\Models\ApplicationForm::where('session', $data['session'])->count() + 1;
        $code = 'ITI-' . $fy . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $advertisementPdf = null;
        if (isset($data['advertisement_pdf_path'])) {
            $path = $data['advertisement_pdf_path'];
            if (str_starts_with($path, 'temp/')) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    $newPath = str_replace('temp/', 'advertisements/', $path);
                    \Illuminate\Support\Facades\Storage::disk('public')->move($path, $newPath);
                    $advertisementPdf = $newPath;
                }
            } else {
                $advertisementPdf = $path;
            }
        }

        $form = \App\Models\ApplicationForm::create([
            'application_code' => $code,
            'application_name' => $data['application_name'],
            'application_description' => $data['application_description'],
            'session' => $data['session'],
            'is_application_fee_required' => $isAppFeeRequired,
            'application_fee' => $data['application_fee'] ?? 0,
            'category_fees' => $data['category_fees'] ?? null,
            'is_admission_required' => $isAdmRequired,
            'admission_fee' => $data['admission_fee'] ?? 0,
            'is_approval_required' => filter_var($data['is_approval_required'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'status' => 'active', 

            'deadline' => $data['deadline'],
            'created_by' => $user->id,
            'field_config' => $data['field_config'] ?? [],
            'institution_id' => $data['institution_id'],
            'organization_id' => $org->id,
            'advertisement_pdf' => $advertisementPdf,
            'no_of_vacancies' => $data['no_of_vacancies'] ?? null,
        ]);

        if ($org->quota) {
            $org->quota->increment('used_forms');
        }

        return ['success' => true, 'form' => $form];
    }

    public function showForm($id)
    {
        $form = \App\Models\ApplicationForm::findOrFail($id);
        
        $org = auth()->user()->currentOrganization();
        if (!$org || $form->organization_id !== $org->id) {
            abort(403, 'Access Denied');
        }
        
        return view('administration.forms.show', compact('form'));
    }

    public function editForm($id)
    {
        $org = auth()->user()->currentOrganization();
        $form = \App\Models\ApplicationForm::findOrFail($id);
        
        // 1. Check Organization Match
        if (!$org || $form->organization_id !== $org->id) {
            abort(403, 'Access Denied');
        }
        
        // 2. Check Admin Role
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only Admins can edit forms.');
        }
        
        $institutions = $org->institutions;
        $globalConfig = \App\Models\GlobalConfig::first();
        $canCreate = $org->canCreateForm();
        $quotaPrice = $globalConfig->form_creation_charge;
        $registrationFeeCharge = $globalConfig->registration_fee_charge;

        // Need columns logic from formBuilder
        // Refactoring to a private method would be better but for now I'll duplicate the column logic 
        // to ensure it works exactly as requested without breaking existing builder.
        $tables = [
            'personal' => 'students',
            'address' => 'addresses',
            'academic' => 'academic_qualifications',
            'parents' => 'student_parents',
            'bank' => 'bank_details',
            'documents' => 'student_documents',
        ];

        $columns = [];
        foreach ($tables as $key => $table) {
            $cols = \Illuminate\Support\Facades\Schema::getColumnListing($table);
            $cols = array_diff($cols, [
                'id', 'user_id', 'created_at', 'updated_at', 'student_id', 'email_verified_at', 'password', 'remember_token', 'role', 'same_as_permanent',
                'marital_status', 'is_ex_serviceman', 'ncc_certificate', 'has_national_sports_achievement', 'has_fine_arts_cultural_achievement',
                'passport', 'transfer_certificate', 'bed_parangat_marksheet', 'bed_parangat_certificate', 'tet_certificate', 'pwd_certificate',
                'ex_serviceman_certificate', 'ncc_certificate_doc', 'sports_certificate', 'cultural_certificate', 'language_diploma_certificate'
            ]);
            
            if ($key === 'personal') {
                $priority = ['full_name', 'phone_number', 'email', 'gender', 'dob', 'category', 'age'];
                $cols = $this->sortColumns($cols, $priority);
                $columns[$key] = $cols;
            } elseif ($key === 'academic') {
                $priority = ['examination', 'board_university', 'passing_year', 'percentage_grade', 'division'];
                $cols = $this->sortColumns($cols, $priority);
                $columns[$key] = $cols;
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

        $orgConfig = $org->configuration ?? new \App\Models\OrganizationConfiguration();
        return view('administration.forms.builder', compact('columns', 'form', 'institutions', 'canCreate', 'quotaPrice', 'registrationFeeCharge', 'org', 'orgConfig'));
    }

    public function updateForm(Request $request, $id)
    {
        $org = auth()->user()->currentOrganization();
        $form = \App\Models\ApplicationForm::findOrFail($id);
        
        // 1. Check Organization Match
        if (!$org || $form->organization_id !== $org->id) {
            abort(403, 'Access Denied');
        }

        // 2. Check Admin Role
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only Admins can update forms.');
        }

        $validated = $request->validate([
            'application_name' => 'required|string|max:255',
            'application_description' => 'nullable|string',
            'session' => 'required|string',
            'is_application_fee_required' => 'boolean',
            'application_fee' => 'nullable|numeric|min:0',
            'category_fees' => 'nullable|array',
            'is_admission_required' => 'boolean',
            'admission_fee' => $request->boolean('is_admission_required') ? 'required|numeric|min:1' : 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'field_config' => 'nullable|array',
            'status' => 'required|in:active,draft,closed',
            'institution_id' => 'required|exists:institutions,id',
            'is_approval_required' => 'boolean',
            'advertisement_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'no_of_vacancies' => 'nullable|integer|min:1',
        ]);

        $updateData = [
            'application_name' => $validated['application_name'],
            'application_description' => $validated['application_description'],
            'session' => $validated['session'],
            'is_application_fee_required' => $request->boolean('is_application_fee_required'),
            'application_fee' => $validated['application_fee'] ?? 0,
            'category_fees' => $validated['category_fees'] ?? null,
            'is_admission_required' => $request->boolean('is_admission_required'),
            'admission_fee' => $validated['admission_fee'] ?? 0,
            'is_approval_required' => $request->boolean('is_approval_required'),
            'deadline' => $validated['deadline'],
            'status' => $validated['status'],
            'field_config' => $validated['field_config'] ?? [],
            'institution_id' => $validated['institution_id'],
            'no_of_vacancies' => $validated['no_of_vacancies'] ?? null,
        ];

        // Handle advertisement PDF upload
        if ($request->hasFile('advertisement_pdf')) {
            // Delete old file if exists
            if ($form->advertisement_pdf && \Illuminate\Support\Facades\Storage::disk('public')->exists($form->advertisement_pdf)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($form->advertisement_pdf);
            }
            $path = $request->file('advertisement_pdf')->store('advertisements', 'public');
            $updateData['advertisement_pdf'] = $path;
        }

        $form->update($updateData);

        return redirect()->route('administration.forms')->with('success', 'Form updated successfully!');
    }

    public function destroyForm($id)
    {
        $org = auth()->user()->currentOrganization();
        $form = \App\Models\ApplicationForm::findOrFail($id);
        
        // 1. Check Organization Match
        if (!$org || $form->organization_id !== $org->id) {
            abort(403, 'Access Denied');
        }

        // 2. Check Admin Role
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only Admins can delete forms.');
        }

        $form->delete();
        return redirect()->route('administration.forms')->with('success', 'Form deleted successfully!');
    }

    public function formApplications()
    {
        return view('administration.forms.applications');
    }

    public function reports(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) {
             return view('administration.reports.index', ['applications' => collect([]), 'forms' => collect([])]);
        }

        $query = \App\Models\StudentApplication::with(['application_student', 'application_form', 'application_address', 'application_documents'])
            ->where('status', '!=', 'draft')
            ->whereHas('application_form', function ($q) use ($org) {
                $q->where('organization_id', $org->id);
            });

        // Search by name/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('application_student', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by Gender
        if ($request->filled('gender')) {
            $query->whereHas('application_student', function($q) use ($request) {
                $q->where('gender', $request->gender);
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->whereHas('application_student', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Filter by Form/Trade
        if ($request->filled('form_id')) {
            $query->where('application_form_id', $request->form_id);
        }

        // Filter by District
        if ($request->filled('district')) {
            $district = $request->district;
            $query->whereHas('application_address', function($q) use ($district) {
                $q->where('present_district', $district)
                  ->orWhere('permanent_district', $district);
            });
        }

        // Filter by Document
        if ($request->filled('document_type') && $request->filled('document_status')) {
            $docType = $request->document_type;
            $docStatus = $request->document_status;
            
            // Whitelist valid columns to prevent SQL injection
            $validDocs = [
                'photo', 'signature', 'hslc_marksheet', 'hs_marksheet', 'graduation_marksheet', 
                'post_graduation_marksheet', 'hslc_pass_certificate', 'hs_pass_certificate', 
                'graduation_pass_certificate', 'post_graduation_pass_certificate', 'hslc_admit_card', 
                'hs_admit_card', 'graduation_admit_card', 'post_graduation_admit_card', 'transfer_certificate', 
                'caste_certificate', 'income_certificate', 'address_proof', 'aadhaar_card', 'passport', 
                'bed_parangat_marksheet', 'bed_parangat_certificate', 'tet_certificate', 'pwd_certificate', 
                'ex_serviceman_certificate', 'ncc_certificate_doc', 'sports_certificate', 
                'cultural_certificate', 'btc_residential_certificate', 'language_diploma_certificate'
            ];

            if (in_array($docType, $validDocs)) {
                if ($docStatus == 'provided') {
                    $query->whereHas('application_documents', function($q) use ($docType) {
                        $q->whereNotNull($docType)->where($docType, '!=', '');
                    });
                } else if ($docStatus == 'not_provided') {
                    // Document relation might not exist, or column might be null
                    $query->where(function($q1) use ($docType) {
                        $q1->doesntHave('application_documents')
                           ->orWhereHas('application_documents', function($q2) use ($docType) {
                               $q2->whereNull($docType)->orWhere($docType, '');
                           });
                    });
                }
            }
        }

        // Filter by Payment Status
        if ($request->filled('payment_status')) {
            if ($request->payment_status == 'paid') {
                $query->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhereHas('application_form', function($subQ) {
                          $subQ->where('is_application_fee_required', false)
                               ->orWhere('application_fee', '<=', 0);
                      });
                });
            } else if ($request->payment_status == 'unpaid') {
                $query->where('payment_status', '!=', 'paid')
                      ->whereHas('application_form', function($subQ) {
                          $subQ->where('is_application_fee_required', true)
                               ->where('application_fee', '>', 0);
                      });
            } else {
                $query->where('payment_status', $request->payment_status);
            }
        } else {
            // Default: Valid if fully paid, OR if the form doesn't require a fee
            $query->where(function($q) {
                $q->where('payment_status', 'paid')
                  ->orWhereHas('application_form', function($subQ) {
                      $subQ->where('is_application_fee_required', false)
                           ->orWhere('application_fee', '<=', 0);
                  });
            });
        }

        // Filter by Approval Status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        $forms = \App\Models\ApplicationForm::where('organization_id', $org->id)->get();

        return view('administration.reports.index', compact('applications', 'forms'));
    }

    public function applications(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) {
             return view('administration.applications.index', ['applications' => collect([]), 'forms' => collect([])]);
        }

        $query = \App\Models\StudentApplication::with(['application_student', 'application_form'])
            ->where('status', '!=', 'draft')
            ->where(function($q) {
                // Valid if fully paid, OR if the form doesn't require a fee
                $q->where('payment_status', 'paid')
                  ->orWhereHas('application_form', function($subQ) {
                      $subQ->where('is_application_fee_required', false)
                           ->orWhere('application_fee', '<=', 0);
                  });
            })
            ->whereHas('application_form', function ($q) use ($org) {
                $q->where('organization_id', $org->id);
            });

        // 1. Filter by Form (Trade)
        if ($request->has('form_id') && $request->form_id != '') {
            $query->where('application_form_id', $request->form_id);
        }

        // 2. Filter by Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by Institution
        if ($request->has('institution_id') && $request->institution_id != '') {
            $query->whereHas('application_form', function ($q) use ($request) {
                $q->where('institution_id', $request->institution_id);
            });
        }

        // Filter by Payment Status
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by Approval Status
        if ($request->has('approval_status') && $request->approval_status != '') {
            $query->where('approval_status', $request->approval_status);
        }

        // 3. Search (Code, Name, Phone, Email)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_code', 'like', "%{$search}%")
                  ->orWhereHas('application_student', function($qs) use ($search) {
                      $qs->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $applications = $query->latest()->paginate(10)->withQueryString();
        $forms = collect([]);
        if ($request->has('institution_id') && $request->institution_id != '') {
             $forms = \App\Models\ApplicationForm::where('organization_id', $org->id)->where('institution_id', $request->institution_id)->select('id', 'application_name')->get();
        } else {
             $forms = $org->applicationForms()->select('id', 'application_name')->get();
        }
        $institutions = $org->institutions()->select('id', 'institution_name')->get();

        return view('administration.applications.index', compact('applications', 'forms', 'institutions'));
    }

    public function exportApplications(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) abort(403);

        $query = \App\Models\StudentApplication::with(['application_student', 'application_form'])
            ->where('status', '!=', 'draft')
            ->where(function($q) {
                // Valid if fully paid, OR if the form doesn't require a fee
                $q->where('payment_status', 'paid')
                  ->orWhereHas('application_form', function($subQ) {
                      $subQ->where('is_application_fee_required', false)
                           ->orWhere('application_fee', '<=', 0);
                  });
            })
            ->whereHas('application_form', function ($q) use ($org) {
                $q->where('organization_id', $org->id);
            });

        // Apply same filters
        if ($request->has('form_id') && $request->form_id != '') {
            $query->where('application_form_id', $request->form_id);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('institution_id') && $request->institution_id != '') {
            $query->whereHas('application_form', function ($q) use ($request) {
                $q->where('institution_id', $request->institution_id);
            });
        }
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->has('approval_status') && $request->approval_status != '') {
            $query->where('approval_status', $request->approval_status);
        }
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_code', 'like', "%{$search}%")
                  ->orWhereHas('application_student', function($qs) use ($search) {
                      $qs->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $applications = $query->latest()->get();

        $csvFileName = 'applications_export_' . date('Y-m-d_H-i') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            
            // Header Row
            fputcsv($file, [
                'Application Code', 
                'Form / Trade', 
                'Applicant Name', 
                'Phone', 
                'Email', 
                'Category', 
                'Status', 
                'Payment Status', 
                'Transaction ID',
                'Submitted At'
            ]);

            // Data Rows
            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->application_code,
                    $app->application_form->application_name ?? 'N/A',
                    $app->application_student->full_name ?? 'N/A',
                    $app->application_student->phone_number ?? 'N/A',
                    $app->application_student->email ?? 'N/A',
                    $app->application_student->category ?? 'N/A',
                    ucfirst($app->status),
                    ucfirst($app->payment_status),
                    $app->transaction_id ?? 'N/A',
                    $app->submitted_at ? $app->submitted_at->format('Y-m-d H:i') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function applicationDetail($id)
    {
        $application = \App\Models\StudentApplication::with([
            'application_form',
            'application_student',
            'application_address',
            'application_parent',
            'application_bank_detail',
            'application_academic_qualifications',
            'application_documents'
        ])->findOrFail($id);

        $org = auth()->user()->currentOrganization();
        if (!$org || $application->application_form->organization_id !== $org->id) {
            abort(403, 'Access Denied');
        }

        return view('administration.applications.detail', compact('application'));
    }

    public function updateApplicationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $application = \App\Models\StudentApplication::with('application_form')->findOrFail($id);
        
        $org = auth()->user()->currentOrganization();
        if (!$org || $application->application_form->organization_id !== $org->id) {
             abort(403, 'Access Denied');
        }
        
        // Only Admin can change status? Or viewers too? Usually Admin.
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only Admins can update application status.');
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

        $application = \App\Models\StudentApplication::with('application_form')->findOrFail($id);
        
        $org = auth()->user()->currentOrganization();
        if (!$org || $application->application_form->organization_id !== $org->id) {
             abort(403, 'Access Denied');
        }
        
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only Admins can update application status.');
        }

        $application->update([
            'approval_status' => $request->approval_status,
            'approval_remarks' => $request->approval_remarks,
        ]);

        return redirect()->back()->with('success', 'Application approval status updated to ' . ucfirst($request->approval_status));
    }

    public function finance(Request $request)
    {
        $institutions = \App\Models\Institution::all();

        $institutionId = $request->get('institution_id');
        $type = $request->get('type'); // 'registration' or 'admission'
        $status = $request->get('status');
        $date = $request->get('date');
        $search = $request->get('search');

        // Combined data query approach
        // 1. Registration Payments
        $registrationQuery = \App\Models\PaymentTransaction::with(['application.student', 'application.application_form.institution']);

        if ($institutionId) {
            $registrationQuery->whereHas('application.application_form', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            });
        }
        if ($status && $status !== 'All Status') {
            $registrationQuery->where('status', $status);
        }
        if ($date) {
            $registrationQuery->whereDate('created_at', $date);
        }
        if ($search) {
            $registrationQuery->where(function ($q) use ($search) {
                $q->where('razorpay_payment_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($o) use ($search) {
                      $o->where('razorpay_order_id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('application.student', function ($s) use ($search) {
                      $s->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Admission Payments
        $admissionQuery = \App\Models\AdmissionPaymentTransaction::with(['order.student_application.student', 'order.application_form.institution']);

        if ($institutionId) {
            $admissionQuery->whereHas('order.application_form', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            });
        }
        if ($status && $status !== 'All Status') {
            $admissionQuery->where('status', $status);
        }
        if ($date) {
            $admissionQuery->whereDate('created_at', $date);
        }
        if ($search) {
            $admissionQuery->where(function ($q) use ($search) {
                $q->where('razorpay_payment_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($o) use ($search) {
                      $o->where('razorpay_order_id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order.student_application.student', function ($s) use ($search) {
                      $s->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        // Handling Type filter
        if ($type === 'registration') {
            $transactions = $registrationQuery->latest()->paginate(20)->withQueryString();
            $transactions->getCollection()->transform(function($t) {
                $t->payment_type = 'Registration';
                $t->student_name = $t->application->student->full_name ?? 'N/A';
                $t->order_id = $t->order->razorpay_order_id ?? 'N/A';
                return $t;
            });
        } elseif ($type === 'admission') {
            $transactions = $admissionQuery->latest()->paginate(20)->withQueryString();
            $transactions->getCollection()->transform(function($t) {
                $t->payment_type = 'Admission';
                $t->student_name = $t->order->student_application->student->full_name ?? 'N/A';
                $t->order_id = $t->order->razorpay_order_id ?? 'N/A';
                return $t;
            });
        } else {
            // Combine both - this is tricky with pagination and different models
            // A simple way is to use union but they have different structures
            // For now, let's fetch both and merge them manually if needed, or just default to showing both in a list
            // Since they have similar structures, we can use map to normalize them

            $reg = $registrationQuery->get()->map(function($t) {
                return [
                    'id' => $t->id,
                    'razorpay_payment_id' => $t->razorpay_payment_id,
                    'order_id' => $t->order->razorpay_order_id ?? 'N/A',
                    'student_name' => $t->application->student->full_name ?? 'N/A',
                    'amount' => $t->amount,
                    'method' => $t->method,
                    'status' => $t->status,
                    'created_at' => $t->created_at,
                    'payment_type' => 'Registration'
                ];
            });

            $adm = $admissionQuery->get()->map(function($t) {
                return [
                    'id' => $t->id,
                    'razorpay_payment_id' => $t->razorpay_payment_id,
                    'order_id' => $t->order->razorpay_order_id ?? 'N/A',
                    'student_name' => $t->order->student_application->student->full_name ?? 'N/A',
                    'amount' => $t->amount,
                    'method' => $t->method,
                    'status' => $t->status,
                    'created_at' => $t->created_at,
                    'payment_type' => 'Admission'
                ];
            });

            $allTransactions = $reg->concat($adm)->sortByDesc('created_at');
            
            // Manual pagination for combined results
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $perPage = 10;
            $currentItems = $allTransactions->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $transactions = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $allTransactions->count(), $perPage, $currentPage, [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]);
        }

        // Stats
        $totalCollected = \App\Models\PaymentTransaction::where('status', 'captured')->sum('amount') + 
                         \App\Models\AdmissionPaymentTransaction::where('status', 'captured')->sum('amount');
        
        $todayCollected = \App\Models\PaymentTransaction::where('status', 'captured')->whereDate('created_at', today())->sum('amount') + 
                         \App\Models\AdmissionPaymentTransaction::where('status', 'captured')->whereDate('created_at', today())->sum('amount');
        
        $pendingPayments = \App\Models\PaymentOrder::where('status', 'created')->sum('amount') + 
                          \App\Models\AdmissionPaymentOrder::where('status', 'created')->sum('amount');

        return view('administration.finance.index', compact('transactions', 'institutions', 'totalCollected', 'todayCollected', 'pendingPayments'));
    }

    public function financeDetail($type, $id)
    {
        $admission_student = null;
        if ($type === 'registration') {
            $transaction = \App\Models\PaymentTransaction::with(['application.student', 'application.application_form.institution', 'order'])->findOrFail($id);
            $order = $transaction->order;
            $purpose_title = "Registration for " . ($transaction->application->application_form->application_name ?? 'N/A');
            $student = $transaction->application->student ?? null;
            $application = $transaction->application;
        } elseif ($type === 'admission') {
            $transaction = \App\Models\AdmissionPaymentTransaction::with(['order.student_application.student', 'order.application_form.institution', 'order'])->findOrFail($id);
            $order = $transaction->order;
            $purpose_title = "Admission for " . ($order->application_form->application_name ?? 'N/A');
            $student = $order->student_application->student ?? null;
            $application = $order->student_application;
            
            // Fetch AdmissionStudent for code
            if ($application) {
                $admission_student = \App\Models\AdmissionStudent::where('student_application_id', $application->id)->first();
            }
        } else {
            abort(404);
        }

        return view('administration.finance.show', compact('transaction', 'order', 'purpose_title', 'student', 'application', 'type', 'admission_student'));
    }

    public function refreshFinanceStatus(Request $request)
    {
        $request->validate([
            'type' => 'required|in:registration,admission',
            'id' => 'required'
        ]);

        $type = $request->type;
        $id = $request->id;

        if ($type === 'registration') {
            $transaction = \App\Models\PaymentTransaction::with('application')->findOrFail($id);
            $application = $transaction->application;
            $order = $transaction->order;
        } else {
            $transaction = \App\Models\AdmissionPaymentTransaction::with('order.student_application')->findOrFail($id);
            $order = $transaction->order;
            $application = $order->student_application;
        }

        if (!$order || !$order->razorpay_order_id) {
            return back()->with('error', 'No Razorpay Order ID found for this transaction.');
        }

        $razorpayKey = config('services.razorpay.key');
        $razorpaySecret = config('services.razorpay.secret');
        $apiAuth = base64_encode($razorpayKey . ':' . $razorpaySecret);

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Basic ' . $apiAuth,
            ])->get('https://api.razorpay.com/v1/orders/' . $order->razorpay_order_id . '/payments');

            if ($response->successful()) {
                $payments = $response->json()['items'] ?? [];
                $updated = false;
                foreach ($payments as $payment) {
                    if ($payment['status'] == 'captured') {
                        // Update Order
                        $order->update([
                            'status' => 'paid',
                            'amount_paid' => $payment['amount'] / 100,
                            'amount_due' => 0
                        ]);

                        // Update Application Status if needed
                        if ($type === 'registration') {
                            $application->update([
                                'payment_status' => 'paid',
                                'transaction_id' => $payment['id'],
                                'status' => 'submitted',
                            ]);
                        } else {
                            // Logic for admission payment update
                            $application->update([
                                'payment_status' => 'paid', // Assuming there's a specific field or logic
                            ]);
                            // If there's an Admission model, update it
                            if ($order->admission_payment) {
                                $order->admission_payment->update(['status' => 'paid']);
                            }
                        }

                        // Update or Create Transaction record
                        if ($type === 'registration') {
                            $transaction->update([
                                'razorpay_payment_id' => $payment['id'],
                                'status' => 'captured',
                                'method' => $payment['method'],
                                'amount' => $payment['amount'] / 100,
                                'fee' => ($payment['fee'] ?? 0) / 100,
                                'tax' => ($payment['tax'] ?? 0) / 100,
                                'json_response' => $payment,
                            ]);
                        } else {
                            $transaction->update([
                                'razorpay_payment_id' => $payment['id'],
                                'status' => 'captured',
                                'method' => $payment['method'],
                                'amount' => $payment['amount'] / 100,
                                'fee' => ($payment['fee'] ?? 0) / 100,
                                'tax' => ($payment['tax'] ?? 0) / 100,
                                'json_response' => $payment,
                            ]);
                        }
                        $updated = true;
                        break;
                    }
                }
                if ($updated) {
                    return back()->with('success', 'Payment status updated successfully!');
                } else {
                    return back()->with('warning', 'No captured payments found on Razorpay for this order.');
                }
            } else {
                return back()->with('error', 'Failed to fetch status from Razorpay: ' . ($response->json()['error']['description'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Razorpay Refresh Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while connecting to Razorpay.');
        }
    }

    public function configuration()
    {
        $org = auth()->user()->currentOrganization();
        $globalConfig = \App\Models\GlobalConfig::first();
        $used = $org->getUsedFormsCount();
        $available = $org->getAvailableFormsCount();
        
        return view('administration.configuration.index', compact('org', 'globalConfig', 'used', 'available'));
    }

    public function initiateIntegratedPayment(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        
        if (!auth()->user()->isAdmin($org->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate advertisement file if present
        $request->validate([
            'advertisement_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $globalConfig = \App\Models\GlobalConfig::first();
        $baseAmount = $globalConfig->form_creation_charge;
        $surcharge = $globalConfig->registration_fee_charge;
        
        // Add surcharge if registration fee is enabled
        $isFeeRequired = filter_var($request->input('is_application_fee_required'), FILTER_VALIDATE_BOOLEAN);
        $amount = $baseAmount + ($isFeeRequired ? $surcharge : 0);
        
        $razorpayKey = config('services.razorpay.key');
        $razorpaySecret = config('services.razorpay.secret');
        $apiAuth = base64_encode($razorpayKey . ':' . $razorpaySecret);

        $receipt = 'integrated_' . $org->id . '_' . time();
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Basic ' . $apiAuth,
        ])->post('https://api.razorpay.com/v1/orders', [
            'amount' => $amount * 100,
            'currency' => 'INR',
            'receipt' => $receipt,
        ]);

        if ($response->successful()) {
            $orderData = $response->json();
            
            $payload = $request->except('advertisement_pdf');
            if ($request->hasFile('advertisement_pdf')) {
                $tempPath = $request->file('advertisement_pdf')->store('temp', 'public');
                $payload['advertisement_pdf_path'] = $tempPath;
            }

            \App\Models\QuotaPaymentOrder::create([
                'organization_id' => $org->id,
                'razorpay_order_id' => $orderData['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'pending',
                'receipt' => $receipt,
                'json_response' => $orderData,
                'pending_payload' => $payload, // Form data (safe for JSON serialization)
            ]);

            return response()->json([
                'order_id' => $orderData['id'],
                'amount' => $amount,
                'key' => $razorpayKey,
                'org_name' => $org->name,
                'email' => auth()->user()->email,
                'contact' => optional(auth()->user()->student)->phone_number ?? '0000000000',
            ]);
        }

        return response()->json([
            'error' => 'Unable to initiate payment: ' . ($response->json()['error']['description'] ?? 'Internal Server Error'),
            'details' => $response->json(),
        ], 500);
    }

    public function verifyQuotaPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        $order = \App\Models\QuotaPaymentOrder::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();
        
        // Verify signature
        $razorpaySecret = config('services.razorpay.secret');
        $generatedSignature = hash_hmac('sha256', $request->razorpay_order_id . '|' . $request->razorpay_payment_id, $razorpaySecret);

        if ($generatedSignature === $request->razorpay_signature) {
            $order->update(['status' => 'paid']);
            
            \App\Models\QuotaPaymentTransaction::create([
                'quota_payment_order_id' => $order->id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'amount' => $order->amount,
                'status' => 'captured',
            ]);

            $quota = \App\Models\OrganizationQuota::firstOrCreate(['organization_id' => $order->organization_id]);
            $quota->increment('available_forms');

            // Handle Integrated Form Creation
            if ($order->pending_payload) {
                $org = $order->organization;
                $user = auth()->user(); // Verification happens in context of logged in user
                
                $res = $this->performFormCreation($order->pending_payload, $org, $user);
                
                if (isset($res['success'])) {
                    $order->update(['application_form_id' => $res['form']->id]);
                    return redirect()->route('administration.forms')->with('success', 'Payment successful and Form created! Code: ' . $res['form']->application_code);
                }
            }

            return redirect()->route('administration.forms')->with('success', 'Quota purchased successfully! You can now create more forms.');
        }

        $order->update(['status' => 'failed']);
        
        \App\Models\QuotaPaymentTransaction::create([
            'quota_payment_order_id' => $order->id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'amount' => $order->amount,
            'status' => 'failed',
            'json_response' => ['error' => 'Signature verification failed']
        ]);

        return redirect()->route('administration.configuration')->with('error', 'Payment verification failed.');
    }

    public function handleQuotaPaymentFailure(Request $request)
    {
        $data = $request->validate([
            'razorpay_order_id' => 'required',
            'error_code' => 'nullable|string',
            'error_description' => 'nullable|string',
            'error_source' => 'nullable|string',
            'error_step' => 'nullable|string',
            'error_reason' => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
        ]);

        $order = \App\Models\QuotaPaymentOrder::where('razorpay_order_id', $data['razorpay_order_id'])->first();

        if ($order) {
            $order->update(['status' => 'failed']);

            \App\Models\QuotaPaymentTransaction::create([
                'quota_payment_order_id' => $order->id,
                'razorpay_payment_id' => $data['razorpay_payment_id'] ?? null,
                'amount' => $order->amount,
                'status' => 'failed',
                'json_response' => $data
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function refreshQuotaPayment($orderId)
    {
        $org = auth()->user()->currentOrganization();
        $order = \App\Models\QuotaPaymentOrder::where('id', $orderId)
            ->where('organization_id', $org->id)
            ->firstOrFail();

        // If already paid, just return
        if ($order->status === 'paid') {
            return back()->with('success', 'Payment is already confirmed.');
        }

        $razorpayKey = config('services.razorpay.key');
        $razorpaySecret = config('services.razorpay.secret');
        $apiAuth = base64_encode($razorpayKey . ':' . $razorpaySecret);

        // Fetch payments for this order
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Basic ' . $apiAuth,
        ])->get("https://api.razorpay.com/v1/orders/{$order->razorpay_order_id}/payments");

        if ($response->successful()) {
            $payments = $response->json();
            
            // Check if any payment is captured
            $capturedPayment = null;
            foreach ($payments['items'] as $payment) {
                if ($payment['status'] === 'captured') {
                    $capturedPayment = $payment;
                    break;
                }
            }

            if ($capturedPayment) {
                \DB::beginTransaction();
                try {
                    $order->update(['status' => 'paid']);
                    
                    // Check if transaction already exists
                    $exists = \App\Models\QuotaPaymentTransaction::where('razorpay_payment_id', $capturedPayment['id'])->exists();
                    
                    if (!$exists) {
                        \App\Models\QuotaPaymentTransaction::create([
                            'quota_payment_order_id' => $order->id,
                            'razorpay_payment_id' => $capturedPayment['id'],
                            'amount' => $order->amount,
                            'status' => 'captured',
                            'json_response' => $capturedPayment
                        ]);

                        $quota = \App\Models\OrganizationQuota::firstOrCreate(['organization_id' => $order->organization_id]);
                        $quota->increment('available_forms');
                    }
                    
                    \DB::commit();
                    return back()->with('success', 'Payment found and confirmed! Your quota has been updated.');
                } catch (\Exception $e) {
                    \DB::rollBack();
                    return back()->with('error', 'Error updating quota: ' . $e->getMessage());
                }
            }
        }

        return back()->with('error', 'No successful payment found for this order ID in Razorpay.');
    }

    public function profile()
    {
        $organization = auth()->user()->currentOrganization();
        return view('administration.profile', compact('organization'));
    }

    public function updateOrganization(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'type' => 'required|string|max:100',
            'industry' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'registration_no' => 'nullable|string|max:100',
            'gst_no' => 'nullable|string|max:20',
            'pan_no' => 'nullable|string|max:20',
            'tan_no' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($org->logo && \Storage::disk('public')->exists($org->logo)) {
                \Storage::disk('public')->delete($org->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $org->update($data);

        return back()->with('success', 'Organization details updated successfully.');
    }
    public function manageUsers()
    {
        $org = auth()->user()->currentOrganization();
        $users = $org->users()->orderBy('created_at', 'desc')->get();
        return view('administration.users.index', compact('users'));
    }

    public function addUser(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only admins can add users.');
        }

        $request->validate([
            'email' => 'nullable|email',
            'role' => 'required|in:admin,viewer',
            'name' => 'nullable|string|max:255', // Only for new users
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            // User exists
            if ($user->organizations()->where('organization_id', $org->id)->exists()) {
                return back()->with('error', 'User is already a member of this organization.');
            }
            // Attach existing user
            $user->organizations()->attach($org->id, ['role' => $request->role]);
            
            // Should we update their main role if they are 'student'? existing logic relies on 'role' column
            // For now, let's assume 'administration' role is required to access dashboard
            if ($user->role === 'student' || $user->role === null) {
                $user->update(['role' => 'administration']);
            }

            return back()->with('success', 'Existing user added successfully.');
        } else {
            // Create new user
            $request->validate(['name' => 'required|string|max:255']);
            
            $password = "password";//\Illuminate\Support\Str::random(10); // Or specific logic
            
            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($password), // We should probably email this
                'role' => 'administration',
            ]);

            $user->organizations()->attach($org->id, ['role' => $request->role]);

            // TODO: Send invitation email with password
            
            return back()->with('success', 'New user created and added. Temporary password generated (logging implementation pending).');
        }
    }

    public function updateUserRole(Request $request, $id)
    {
        $org = auth()->user()->currentOrganization();
        
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Unauthorized');
        }

        $user = $org->users()->findOrFail($id);
        
        // Prevent removing own admin access if only admin? For now simple check
        if ($user->id === auth()->id() && $request->role !== 'admin') {
             // Check if there are other admins
             $adminCount = $org->users()->wherePivot('role', 'admin')->count();
             if ($adminCount <= 1) {
                 return back()->with('error', 'You are the only admin. You cannot demote yourself.');
             }
        }

        $org->users()->updateExistingPivot($id, ['role' => $request->role]);
        
        return back()->with('success', 'User role updated.');
    }

    public function removeUser($id)
    {
        $org = auth()->user()->currentOrganization();
        
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Unauthorized');
        }

        $user = $org->users()->findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove yourself.');
        }

        $org->users()->detach($id);
        
        return back()->with('success', 'User removed from organization.');
    }

    public function quotaTransactions()
    {
        $org = auth()->user()->currentOrganization();
        
        $transactions = \App\Models\QuotaPaymentOrder::where('organization_id', $org->id)
            ->with(['applicationForm', 'transactions' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('administration.configuration.quota-transactions', compact('transactions'));
    }

    public function quotaInvoice($transactionId)
    {
        $org = auth()->user()->currentOrganization();
        
        $transaction = \App\Models\QuotaPaymentTransaction::with(['order.organization', 'order.applicationForm'])
            ->whereHas('order', function($q) use ($org) {
                $q->where('organization_id', $org->id);
            })
            ->findOrFail($transactionId);

        $globalConfig = \App\Models\GlobalConfig::first();

        return view('administration.configuration.quota-invoice', compact('transaction', 'org', 'globalConfig'));
    }

    public function downloadQuotaInvoice($transactionId)
    {
        $org = auth()->user()->currentOrganization();
        
        $transaction = \App\Models\QuotaPaymentTransaction::with(['order.organization', 'order.applicationForm'])
            ->whereHas('order', function($q) use ($org) {
                $q->where('organization_id', $org->id);
            })
            ->findOrFail($transactionId);

        $globalConfig = \App\Models\GlobalConfig::first();

        $pdf = Pdf::loadView('administration.configuration.quota-invoice-pdf', compact('transaction', 'org', 'globalConfig'));
        
        return $pdf->download('Invoice_' . $transaction->order->razorpay_order_id . '.pdf');
    }

    public function getFormLinks($formId)
    {
        $org = auth()->user()->currentOrganization();
        $form = ApplicationForm::where('organization_id', $org->id)->findOrFail($formId);
        
        $links = $form->formLinks()->orderBy('created_at', 'desc')->get();
        
        if ($links->isEmpty()) {
            $linkCode = \Illuminate\Support\Str::random(12);
            while (\App\Models\FormLink::where('link_code', $linkCode)->exists()) {
                $linkCode = \Illuminate\Support\Str::random(12);
            }

            $link = $form->formLinks()->create([
                'link_code' => $linkCode,
                'description' => 'Default Link',
                'is_active' => true,
            ]);
            $links = collect([$link]);
        }
        
        return response()->json([
            'success' => true,
            'links' => $links,
            'form_name' => $form->application_name
        ]);
    }

    public function addFormLink(Request $request)
    {
        $request->validate([
            'application_form_id' => 'required|exists:application_forms,id',
            'description' => 'required|string|max:255',
        ]);

        $org = auth()->user()->currentOrganization();
        $form = ApplicationForm::where('organization_id', $org->id)->findOrFail($request->application_form_id);

        $linkCode = \Illuminate\Support\Str::random(12);
        
        // Ensure uniqueness
        while (\App\Models\FormLink::where('link_code', $linkCode)->exists()) {
            $linkCode = \Illuminate\Support\Str::random(12);
        }

        $link = $form->formLinks()->create([
            'link_code' => $linkCode,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Link created successfully.',
            'link' => $link
        ]);
    }

    public function toggleFormLink($id)
    {
        $org = auth()->user()->currentOrganization();
        $link = \App\Models\FormLink::whereHas('applicationForm', function($q) use ($org) {
            $q->where('organization_id', $org->id);
        })->findOrFail($id);

        $link->is_active = !$link->is_active;
        $link->save();

        return response()->json([
            'success' => true,
            'message' => 'Link status updated.',
            'is_active' => $link->is_active
        ]);
    }

    public function deleteFormLink($id)
    {
        $org = auth()->user()->currentOrganization();
        $link = \App\Models\FormLink::whereHas('applicationForm', function($q) use ($org) {
            $q->where('organization_id', $org->id);
        })->findOrFail($id);

        // Don't allow deleting the last link? Or just delete it.
        $formId = $link->application_form_id;
        $linkCount = \App\Models\FormLink::where('application_form_id', $formId)->count();
        
        if ($linkCount <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the only remaining link for this form.'
            ], 422);
        }

        $link->delete();

        return response()->json([
            'success' => true,
            'message' => 'Link deleted successfully.'
        ]);
    }

    public function generateQrCode($id)
    {
        $org = auth()->user()->currentOrganization();
        $link = \App\Models\FormLink::whereHas('applicationForm', function($q) use ($org) {
            $q->where('organization_id', $org->id);
        })->findOrFail($id);

        $url = url('/f/' . $link->link_code);
        
        // Check if GD or Imagick is available for PNG, otherwise fallback to SVG
        try {
            return response(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->margin(1)->generate($url))
                ->header('Content-Type', 'image/png');
        } catch (\Exception $e) {
            return response(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->margin(1)->generate($url))
                ->header('Content-Type', 'image/svg+xml');
        }
    }
}
