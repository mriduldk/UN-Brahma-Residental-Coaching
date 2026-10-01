<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\RazorpayService;
use App\Models\AdmissionStudent;
use App\Models\AdmissionPaymentOrder;
use App\Models\AdmissionPaymentTransaction;
use App\Models\AdmissionPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }
    public function dashboard()
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();

        // 1. Fetch Applications (My Applications)
        $myApplications = \App\Models\StudentApplication::with(['application_form'])
                            ->where('student_id', $student->id)
                            ->orderBy('updated_at', 'desc')
                            ->get();

        // 2. Calculate Stats
        $stats = [
            'draft' => $myApplications->filter(function($app) {
                $form = $app->application_form;
                $isExpired = $form->isExpired();
                return $app->status == 'draft' && !$isExpired;
            })->count(),
            'submitted' => $myApplications->whereIn('status', ['submitted', 'under_review'])->count(),
            'approved' => $myApplications->where('status', 'approved')->count(),
            'rejected' => $myApplications->where('status', 'rejected')->count(),
            'payment_pending' => $myApplications->filter(function($app) use ($student) {
                $form = $app->application_form;
                $isExpired = $form->isExpired();
                $feeRequired = $form->is_application_fee_required && $form->getApplicationFeeFor($student, $app) > 0;
                return !$isExpired && $feeRequired && $app->payment_status == 'pending' && $app->status != 'draft';
            })->count(),
            'total' => $myApplications->count()
        ];

        // Leave Stats for School Management
        $enrollment = \App\Models\School\SchoolEnrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        $leaveStats = [
            'pending' => 0,
            'approved' => 0,
        ];

        if ($enrollment) {
            $leaveStats['pending'] = \App\Models\School\Leave::where('leavable_id', $enrollment->id)
                ->where('leavable_type', \App\Models\School\SchoolEnrollment::class)
                ->where('status', 'pending')
                ->count();
            
            $leaveStats['approved'] = \App\Models\School\Leave::where('leavable_id', $enrollment->id)
                ->where('leavable_type', \App\Models\School\SchoolEnrollment::class)
                ->where('status', 'approved')
                ->count();
        }

        // 3. Pending Actions (Drafts or Payment Pending)
        $pendingActions = $myApplications->filter(function($app) use ($student) {
            $form = $app->application_form;
            $isExpired = $form->isExpired();
            
            if ($isExpired) {
                return false;
            }

            $isDraft = $app->status == 'draft';
            
            $feeRequired = $form->is_application_fee_required && $form->getApplicationFeeFor($student, $app) > 0;
            $isPaymentPending = $feeRequired && $app->payment_status == 'pending' && $app->status != 'draft';

            return $isDraft || $isPaymentPending;
        })->take(3); // Limit to top 3

        // 4. Upcoming Deadlines (Active forms with future deadlines, including today until 11:59 PM)
        $upcomingDeadlines = \App\Models\ApplicationForm::where('status', 'active')
                                ->whereDate('deadline', '>=', today())
                                ->orderBy('deadline', 'asc')
                                ->take(3)
                                ->get();

        // 5. Enrolled Courses / Admissions
        $admissions = \App\Models\AdmissionStudent::with(['institution', 'application_form'])
                                ->where('student_id', $student->id)
                                ->get();

        return view('student.dashboard', compact('student', 'stats', 'leaveStats', 'pendingActions', 'upcomingDeadlines', 'admissions'));
    }

    public function allApplications(Request $request)
    {
        $search = $request->input('search');
        $instituteFilter = $request->input('institute_id');

        $query = \App\Models\ApplicationForm::where('status', '!=', 'draft')
                                            ->where(function($q) {
                                                $q->whereNull('deadline')
                                                  ->orWhere('deadline', '>=', now()->subDays(7));
                                            });

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('application_name', 'like', "%{$search}%")
                  ->orWhere('application_code', 'like', "%{$search}%");
            });
        }

        // Dropdown Institute filter
        if (!empty($instituteFilter)) {
            $query->where('institution_id', $instituteFilter);
        }

        $forms = $query->orderBy('created_at', 'desc')->paginate(9)->withQueryString();

        // Fetch all active institutions for the filter dropdown
        $allActiveInstitutions = \App\Models\Institution::whereHas('application_forms', function($q) {
            $q->where('status', 'active');
        })->orderBy('institution_name')->get();
        
        $user = auth()->user();
        $student = clone $user ? \App\Models\Student::where('user_id', $user->id)->first() : null;
        
        $studentApplications = [];
        if ($student) {
            $studentApplications = \App\Models\StudentApplication::where('student_id', $student->id)
                                ->get()
                                ->keyBy('application_form_id');
        }

        return view('student.applications.institutes', compact('forms', 'allActiveInstitutions', 'search', 'instituteFilter', 'studentApplications', 'student'));
    }

    public function instituteApplications($id)
    {
        $institution = \App\Models\Institution::findOrFail($id);
        $forms = \App\Models\ApplicationForm::where('institution_id', $id)
                                            ->where('status', '!=', 'draft')
                                            ->where(function($query) {
                                                $query->whereNull('deadline')
                                                      ->orWhere('deadline', '>=', now()->subDays(7));
                                            })
                                            ->orderBy('created_at', 'desc')
                                            ->get();

        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        
        $studentApplications = [];
        if ($student) {
            $studentApplications = \App\Models\StudentApplication::where('student_id', $student->id)
                                ->get()
                                ->keyBy('application_form_id');
        }

        return view('student.applications.index', compact('forms', 'institution', 'studentApplications', 'student'));
    }

    public function myApplications()
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();
        
        $applications = collect();
        if ($student) {
            $applications = \App\Models\StudentApplication::with(['application_form', 'student', 'application_student'])
                                ->where('student_id', $student->id)
                                ->orderBy('created_at', 'desc')
                                ->get();
        }

        return view('student.applications.my', compact('applications'));
    }

    public function profile()
    {
        $user = auth()->user();
        $student = \App\Models\Student::with(['address', 'parent', 'bank_detail', 'academic_qualifications', 'document'])->where('user_id', $user->id)->first();
        
        if (!$student) {
            if ($user->role === 'student') {
                return redirect()->route('student.onboarding');
            }
            abort(403, 'Student profile not found.');
        }

        return view('student.profile', compact('student'));
    }

    public function editProfile()
    {
        $user = auth()->user();
        $student = \App\Models\Student::with(['address', 'parent', 'bank_detail', 'academic_qualifications', 'document'])->where('user_id', $user->id)->first();
        
        if (!$student) {
            if ($user->role === 'student') {
                return redirect()->route('student.onboarding');
            }
            abort(403, 'Student profile not found.');
        }

        $communities = \App\Models\Community::active()->get();
        $btclaConstituencies = \App\Models\BtclaConstituency::active()->get();
        return view('student.edit-profile', compact('student', 'communities', 'btclaConstituencies'));
    }

    public function updateProfile(Request $request) {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'category' => 'required|string',
            'permanent_district' => 'nullable|in:Kokrajhar,Chirang,Baksa,Tamulpur,Udalguri',
        ], [
            'permanent_district.in' => 'Permanent Address District must be one of: Kokrajhar, Chirang, Baksa, Tamulpur, Udalguri.',
        ]);

        // 1. Update Student Personal Details
        $student->update([
            'full_name' => $request->full_name,
            'phone_number' => $student->phone_number,
            'email' => $student->email,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'age' => $request->age,
            'blood_group' => $request->blood_group,
            'category' => $request->category,
            'is_pwd' => $request->boolean('is_pwd'),
            'religion' => $request->religion,
            'community' => $request->community ?? $request->nationality,
            'nationality' => $request->community ?? $request->nationality,
            'mother_tongue' => $request->mother_tongue,
            'identification_mark' => $request->identification_mark,
            'employment_exchange_no' => $request->employment_exchange_no,
            'is_permanent_resident_of_assam' => $request->filled('is_permanent_resident_of_assam') ? $request->boolean('is_permanent_resident_of_assam') : null,
            'is_btc_resident' => $request->filled('is_btc_resident') ? $request->boolean('is_btc_resident') : null,
            'marital_status' => $request->marital_status,
            'alternate_mobile_number' => $request->alternate_mobile_number,
            'is_ex_serviceman' => $request->has('is_ex_serviceman') ? $request->boolean('is_ex_serviceman') : null,
            'ncc_certificate' => $request->ncc_certificate,
            'has_national_sports_achievement' => $request->has('has_national_sports_achievement') ? $request->boolean('has_national_sports_achievement') : null,
            'has_fine_arts_cultural_achievement' => $request->has('has_fine_arts_cultural_achievement') ? $request->boolean('has_fine_arts_cultural_achievement') : null,
        ]);

        // 2. Update Address
        \App\Models\Address::updateOrCreate(
            ['student_id' => $student->id],
            [
                'present_village_town' => $request->present_village_town,
                'present_street' => $request->present_street,
                'present_city' => $request->present_city,
                'present_district' => $request->present_district,
                'present_state' => $request->present_state ?? null,
                'present_country' => $request->present_country,
                'present_pin_code' => $request->present_pin_code,
                'permanent_village_town' => $request->permanent_village_town,
                'permanent_street' => $request->permanent_street,
                'permanent_city' => $request->permanent_city,
                'permanent_district' => $request->permanent_district,
                'permanent_state' => 'Assam',
                'permanent_country' => $request->permanent_country,
                'permanent_pin_code' => $request->permanent_pin_code,
            ]
        );

        // 3. Update Parents
        \App\Models\StudentParent::updateOrCreate(
            ['student_id' => $student->id],
            [
                'father_name' => $request->father_name,
                'father_mobile' => $request->father_mobile,
                'father_email' => $request->father_email,
                'father_occupation' => $request->father_occupation,
                'father_qualification' => $request->father_qualification,
                'father_annual_income' => $request->father_annual_income,
                'mother_name' => $request->mother_name,
                'mother_mobile' => $request->mother_mobile,
                'mother_email' => $request->mother_email,
                'mother_occupation' => $request->mother_occupation,
                'mother_qualification' => $request->mother_qualification,
                'mother_annual_income' => $request->mother_annual_income,
                'guardian_name' => $request->guardian_name,
                'guardian_relation' => $request->guardian_relation,
                'guardian_mobile' => $request->guardian_mobile,
                'guardian_occupation' => $request->guardian_occupation,
                'guardian_address' => $request->guardian_address,
            ]
        );

        // 4. Update Bank Details
        \App\Models\BankDetail::updateOrCreate(
            ['student_id' => $student->id],
            [
                'account_holder_name' => $request->account_holder_name,
                'bank_name' => $request->bank_name,
                'branch' => $request->branch,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'upi_id' => $request->upi_id,
            ]
        );

        // 5. Update Academic Qualifications
        // Strategy: Delete existing and recreate from form data
        $student->academic_qualifications()->delete();
        if ($request->has('qual') && is_array($request->qual)) {
            foreach ($request->qual as $q) {
                if (!empty($q['exam'])) {
                    $student->academic_qualifications()->create([
                        'examination' => $q['exam'],
                        'passing_year' => $q['year'],
                        'board_university' => $q['board'],
                        'roll_number' => $q['roll_no'] ?? null,
                        'registration_number' => $q['reg_no'] ?? null,
                        'medium_of_study' => $q['medium'] ?? null,
                        'total_marks' => $q['total_marks'] ?? null,
                        'marks_obtained' => $q['marks_obtained'] ?? null,
                        'percentage_grade' => $q['marks'],
                        'division' => $q['div'],
                    ]);
                }
            }
        }

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }

    public function onboarding()
    {
        return view('student.onboarding');
    }

    public function storeOnboarding(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'category' => 'required|string',
            'age' => 'nullable|integer',
            'blood_group' => 'nullable|string',
            'religion' => 'nullable|string',
            'community' => 'nullable|string',
            'nationality' => 'nullable|string',
            'mother_tongue' => 'nullable|string',
            'identification_mark' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'alternate_mobile_number' => 'nullable|string',
        ]);

        \App\Models\Student::create([
            'user_id' => $user->id,
            'full_name' => $request->full_name,
            'phone_number' => $user->mobile_number,
            'email' => $user->email,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'age' => $request->age,
            'blood_group' => $request->blood_group,
            'category' => $request->category,
            'is_pwd' => $request->boolean('is_pwd'),
            'religion' => $request->religion,
            'community' => $request->community ?? $request->nationality,
            'nationality' => $request->community ?? $request->nationality,
            'mother_tongue' => $request->mother_tongue,
            'identification_mark' => $request->identification_mark,
            'employment_exchange_no' => $request->employment_exchange_no,
            'is_permanent_resident_of_assam' => $request->filled('is_permanent_resident_of_assam') ? $request->boolean('is_permanent_resident_of_assam') : null,
            'is_btc_resident' => $request->filled('is_btc_resident') ? $request->boolean('is_btc_resident') : null,
            'marital_status' => $request->marital_status,
            'alternate_mobile_number' => $request->alternate_mobile_number,
            'is_ex_serviceman' => $request->has('is_ex_serviceman') ? $request->boolean('is_ex_serviceman') : null,
            'ncc_certificate' => $request->ncc_certificate,
            'has_national_sports_achievement' => $request->has('has_national_sports_achievement') ? $request->boolean('has_national_sports_achievement') : null,
            'has_fine_arts_cultural_achievement' => $request->has('has_fine_arts_cultural_achievement') ? $request->boolean('has_fine_arts_cultural_achievement') : null,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Profile setup complete!');
    }

    public function applicationForm(Request $request)
    {
        $form_id = $request->query('form_id');

        if (!$form_id) {
            return redirect()->route('student.applications.index')->with('error', 'Please select an application to apply.');
        }

        $user = auth()->user();
        $student = \App\Models\Student::with(['address', 'parent', 'bank_detail', 'academic_qualifications', 'document'])
                                    ->firstOrCreate(['user_id' => $user->id], [
                                        'full_name' => $user->name,
                                        'phone_number' => $user->mobile_number,
                                        'email' => $user->email,
                                    ]);

        // Check for existing application with snapshots
        $existingApp = \App\Models\StudentApplication::with(['application_student', 'application_address', 'application_parent', 'application_bank_detail', 'application_academic_qualifications', 'application_documents'])
                                                    ->where('student_id', $student->id)
                                                    ->where('application_form_id', $form_id)
                                                    ->whereIn('status', ['draft', 'pending', 'submitted', 'approved'])
                                                    ->first();                                                    

        if ($existingApp) {
            if ($existingApp->status == 'draft') {
                // Continue draft (Logic could be added here to load saved snapshot if we were saving to snapshot tables incrementally, 
                // but currently we save to Profile tables until submit, so we just show the form populated from Profile)
            } else {
                // Redirect to Read-Only View
                return redirect()->route('student.application.show', $existingApp->id);
            }
        } else {
            // Create Draft
           $existingApp = \App\Models\StudentApplication::create([
                'application_code' => null, // Generated only on final submit
                'application_form_id' => $form_id,
                'student_id' => $student->id,
                'status' => 'draft',
                'current_step' => 1,
            ]);
        }

        $form = \App\Models\ApplicationForm::where('status', 'active')->findOrFail($form_id);

        if ($form->isDeadlinePassed()) {
            return redirect()->route('student.applications.history')->with('error', 'The deadline for this application has passed.');
        }

        $communities = \App\Models\Community::active()->get();
        $btclaConstituencies = \App\Models\BtclaConstituency::active()->get();
        $examinationCenters = ['Kokrajhar', 'Chirang', 'Baksa', 'Tamulpur', 'Udalguri'];
        return view('student.application.form', compact('form', 'existingApp', 'student', 'communities', 'btclaConstituencies', 'examinationCenters'));
    }

    public function showApplication($id)
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        
        $application = \App\Models\StudentApplication::with(['application_student', 'application_address', 'application_parent', 'application_bank_detail', 'application_academic_qualifications', 'application_documents', 'application_form', 'payment_transactions', 'latest_payment_order'])
                                                    ->findOrFail($id);

        if ($application->student_id != $student->id) {
            abort(403, 'Access Denied');
        }

        // Prepare data array similar to preview for consistent view logic
        $data = [
            'trade' => $application->application_form->application_name ?? 'N/A',
            'applied_for' => $application->applied_for ?? $application->application_student?->applied_for ?? $application->application_form->application_name ?? 'N/A',
            'examination_center' => $application->examination_center ?? $application->application_student?->examination_center ?? 'N/A',
            'full_name' => $application->application_student->full_name ?? '',
            'phone_number' => $application->application_student->phone_number ?? '',
            'email' => $application->application_student->email ?? '',
            'dob' => $application->application_student->dob ?? '',
            'category' => $application->application_student->category ?? '',
            
            'father_name' => $application->application_parent->father_name ?? '',
            'father_mobile' => $application->application_parent->father_mobile ?? '',
            'mother_name' => $application->application_parent->mother_name ?? '',
            'guardian_name' => $application->application_parent->guardian_name ?? '',
            
            'present_village_town' => $application->application_address->present_village_town ?? '',
            'present_street' => $application->application_address->present_street ?? '',
            'present_district' => $application->application_address->present_district ?? '',
            'present_state' => $application->application_address->present_state ?? '',
            'present_pin_code' => $application->application_address->present_pin_code ?? '',
            
            'permanent_village_town' => $application->application_address->permanent_village_town ?? '',
            'permanent_street' => $application->application_address->permanent_street ?? '',
            'permanent_district' => $application->application_address->permanent_district ?? '',
            'permanent_state' => $application->application_address->permanent_state ?? '',
            'permanent_pin_code' => $application->application_address->permanent_pin_code ?? '',
            
            'account_holder_name' => $application->application_bank_detail->account_holder_name ?? '',
            'bank_name' => $application->application_bank_detail->bank_name ?? '',
            'branch' => $application->application_bank_detail->branch ?? '',
            'account_number' => $application->application_bank_detail->account_number ?? '',
            'ifsc_code' => $application->application_bank_detail->ifsc_code ?? '',
            'upi_id' => $application->application_bank_detail->upi_id ?? '',

            'qual' => $application->application_academic_qualifications->map(function($q) {
                return [
                    'exam' => $q->examination,
                    'year' => $q->passing_year,
                    'board' => $q->board_university,
                    'marks' => $q->percentage_grade,
                    'div' => $q->division,
                ];
            })->toArray(),
        ];

        return view('student.application.show', compact('application', 'data'));
    }

    public function destroyApplication($id)
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();

        $application = \App\Models\StudentApplication::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($application->status !== 'draft') {
            return redirect()->route('student.applications.history')
                ->with('error', 'Only draft applications can be deleted.');
        }

        $application->delete();

        return redirect()->route('student.applications.history')
            ->with('success', 'Draft application deleted successfully.');
    }

    public function downloadApplicationPdf($id)
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        
        $application = \App\Models\StudentApplication::with([
            'application_student', 
            'application_address', 
            'application_parent', 
            'application_bank_detail', 
            'application_academic_qualifications', 
            'application_documents', 
            'application_form.institution', 
            'payment_transactions'
        ])->findOrFail($id);

        if ($application->student_id != $student->id) {
            abort(403, 'Access Denied');
        }

        $pdf = Pdf::loadView('student.application.pdf', compact('application'))->setPaper('a3', 'portrait');
        
        // Use a clean filename
        $filename = 'Application_' . str_replace('/', '_', $application->application_code) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function downloadAcknowledgment($id)
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        
        $application = \App\Models\StudentApplication::with([
            'application_form.institution.organization',
            'application_student',
            'application_address',
            'application_parent',
            'application_academic_qualifications',
            'admission_student',
            'admission_payments.order.latest_transaction'
        ])->findOrFail($id);

        if ($application->student_id != $student->id) {
            abort(403, 'Access Denied');
        }

        if (!$application->admission_student) {
            return redirect()->back()->with('error', 'Admission record not found for this student.');
        }

        $institution = $application->application_form->institution;

        $data = [
            'application' => $application,
            'student' => $application->application_student,
            'admission' => $application->admission_student,
            'institution' => $institution,
            'organization' => $institution->organization,
            'payments' => $application->admission_payments()->orderBy('created_at', 'desc')->get(),
        ];

        $pdf = Pdf::loadView('institute.admissions.acknowledgment', $data);
        
        $filename = 'Acknowledgment_' . str_replace('/', '_', $application->admission_student->admission_code) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function applicationPreview(Request $request)
    {
        if ($request->isMethod('get')) {
            $data = session('application_data') ?? [];
            $app = null;
            $user = auth()->user();
            $student = \App\Models\Student::where('user_id', $user->id)->first();

            $appId = $data['application_id'] ?? $request->query('application_id');
            if ($appId) {
                $app = \App\Models\StudentApplication::with(['application_form', 'application_documents', 'application_student'])
                    ->where('id', $appId)
                    ->when($student, fn($q) => $q->where('student_id', $student->id))
                    ->first();
            }

            // Fallback 1: check by form_id for current student
            if (!$app && $student) {
                $formId = $request->query('form_id') ?? ($data['form_id'] ?? null);
                if ($formId) {
                    $app = \App\Models\StudentApplication::with(['application_form', 'application_documents', 'application_student'])
                        ->where('student_id', $student->id)
                        ->where('application_form_id', $formId)
                        ->latest()
                        ->first();
                }
            }

            // Fallback 2: student's latest application
            if (!$app && $student) {
                $app = \App\Models\StudentApplication::with(['application_form', 'application_documents', 'application_student'])
                    ->where('student_id', $student->id)
                    ->latest()
                    ->first();
            }

            // If app was found and is ALREADY submitted, approved, or pending:
            if ($app && in_array($app->status, ['submitted', 'pending', 'approved'])) {
                return redirect()->route('student.application.show', $app->id)
                    ->with('success', 'Application has already been submitted successfully.');
            }

            if (!$app) {
                return redirect()->route('student.applications.history')
                    ->with('info', 'Please select an application to continue or review.');
            }

            $data['application_id'] = $app->id;
            $data['form_id'] = $app->application_form_id;
            session(['application_data' => $data]);

            if (empty($data['applied_for'])) {
                $data['applied_for'] = $app->applied_for ?? $app->application_student?->applied_for ?? $app->application_form?->application_name;
            }
            if (empty($data['examination_center'])) {
                $data['examination_center'] = $app->examination_center ?? $app->application_student?->examination_center ?? '';
            }
            if (empty($data['full_name'])) {
                $data['full_name'] = $app->application_student?->full_name ?? auth()->user()->name ?? '';
            }
            
            return view('student.application.preview', compact('data', 'app'));
        }

        // POST Request Handling
        $request->validate([
            'photo' => 'nullable|file|max:200',
            'caste_certificate' => 'nullable|file|max:200',
            'signature' => 'nullable|file|max:200',
            // Add others validation widely or iterate
        ]);

        $data = $request->all();

        // Ensure application_id is passed if available
        $app = null;
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();

        if (isset($data['application_id'])) {
            $app = \App\Models\StudentApplication::with(['application_form', 'application_documents'])
                ->where('id', $data['application_id'])
                ->when($student, fn($q) => $q->where('student_id', $student->id))
                ->first();
        } elseif (isset($data['form_id']) && $student) {
            $app = \App\Models\StudentApplication::with(['application_form', 'application_documents'])
                ->where('student_id', $student->id)
                ->where('application_form_id', $data['form_id'])
                ->where('status', 'draft')
                ->latest()
                ->first();

            // If no draft found, maybe it was already submitted?
            if (!$app) {
                $submittedApp = \App\Models\StudentApplication::where('student_id', $student->id)
                    ->where('application_form_id', $data['form_id'])
                    ->whereIn('status', ['submitted', 'pending', 'approved'])
                    ->latest()
                    ->first();
                if ($submittedApp) {
                    return redirect()->route('student.application.show', $submittedApp->id)
                        ->with('success', 'Application has already been submitted successfully.');
                }
            }
        }

        if ($app) {
            $data['application_id'] = $app->id;

            if ($request->filled('applied_for')) {
                $data['applied_for'] = $request->applied_for;
                if (\Illuminate\Support\Facades\Schema::hasColumn('student_applications', 'applied_for')) {
                    $app->update(['applied_for' => $request->applied_for]);
                }
                if ($app->application_student && \Illuminate\Support\Facades\Schema::hasColumn('application_students', 'applied_for')) {
                    $app->application_student->update(['applied_for' => $request->applied_for]);
                }
            } else {
                $data['applied_for'] = $app->applied_for ?? $app->application_student?->applied_for ?? ($data['applied_for'] ?? '');
            }

            if ($request->filled('examination_center')) {
                $data['examination_center'] = $request->examination_center;
                if (\Illuminate\Support\Facades\Schema::hasColumn('student_applications', 'examination_center')) {
                    $app->update(['examination_center' => $request->examination_center]);
                }
                if ($app->application_student && \Illuminate\Support\Facades\Schema::hasColumn('application_students', 'examination_center')) {
                    $app->application_student->update(['examination_center' => $request->examination_center]);
                }
            } else {
                $data['examination_center'] = $app->examination_center ?? $app->application_student?->examination_center ?? ($data['examination_center'] ?? '');
            }

            $appliedForVal = $data['applied_for'] ?? ($app->applied_for ?? '');
            
            // Handle Document Uploads - Validate and Save to DB directly
            $form = \App\Models\ApplicationForm::find($app->application_form_id);
            $docConfig = $form->field_config['documents']['fields'] ?? [];
            if (empty($docConfig)) {
                $docConfig = ['photo', 'caste_certificate', 'signature', 'prc_certificate', 'age_proof', 'mark_sheet_10', 'certificate_10'];
            }
            $docConfig = array_values(array_filter($docConfig, fn($f) => $form->isFieldVisible('documents', $f)));

            \Illuminate\Support\Facades\Log::info('AppPreview: Processing Uploads for App ID: ' . $app->id);
            \Illuminate\Support\Facades\Log::info('AppPreview: Doc Config: ' . json_encode($docConfig));

            // Map Application Document Types to DB Columns (Consistency with saveStep)
            $docMapping = [
                'photo' => 'photo',
                'signature' => 'signature',
                'caste_certificate' => 'caste_certificate',
                'mark_sheet_10' => 'hslc_marksheet',
                'hslc_marksheet' => 'hslc_marksheet',
                'certificate_10' => 'hslc_pass_certificate',
                'hslc_pass_certificate' => 'hslc_pass_certificate',
                'mark_sheet_12' => 'hs_marksheet',
                'hs_marksheet' => 'hs_marksheet',
                'certificate_12' => 'hs_pass_certificate',
                'hs_pass_certificate' => 'hs_pass_certificate',
                'graduation_marksheet' => 'graduation_marksheet',
                'graduation_certificate' => 'graduation_pass_certificate',
                'graduation_pass_certificate' => 'graduation_pass_certificate',
                'post_graduation_marksheet' => 'post_graduation_marksheet',
                'post_graduation_certificate' => 'post_graduation_pass_certificate',
                'post_graduation_pass_certificate' => 'post_graduation_pass_certificate',
                'prc_certificate' => 'address_proof', 
                'address_proof' => 'address_proof',
                'age_proof' => 'hslc_admit_card', 
                'hslc_admit_card' => 'hslc_admit_card',
                'hs_admit_card' => 'hs_admit_card',
                'graduation_admit_card' => 'graduation_admit_card',
                'post_graduation_admit_card' => 'post_graduation_admit_card',
                'income_certificate' => 'income_certificate',
                'aadhaar_card' => 'aadhaar_card',
                'btc_residential_certificate' => 'btc_residential_certificate',
            ];

            $optionalDocs = ['caste_certificate'];

            $appDoc = \App\Models\ApplicationDocument::firstOrCreate(['student_application_id' => $app->id]);
            $updateData = [];

            foreach ($docConfig as $docType) {
                $dbColumn = $docMapping[$docType] ?? $docType;

                if ($request->hasFile($docType)) {
                    // Server-side size validation 
                    if ($request->file($docType)->getSize() > 200 * 1024) {
                        return back()->withInput()->with('error', "File $docType is too large. Max 200KB allowed.");
                    }

                    $file = $request->file($docType);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $dbColumn . '_application_' . $app->application_form_id . '_' . $app->student_id . '.' . $extension;
                    
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists('documents/' . $app->id . '/' . $filename)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete('documents/' . $app->id . '/' . $filename);
                    }

                    $path = $file->storeAs('documents/' . $app->id, $filename, 'public');
                    
                    $updateData[$dbColumn] = $path;
                    $appDoc->$dbColumn = $path; // Temp update for validation check below
                    \Illuminate\Support\Facades\Log::info("AppPreview: Uploaded $docType (Custom Name) to $path (Mapped to $dbColumn)");
                } else {
                    // No new file and no existing doc in app snapshot. Check Profile.
                    $profileField = $docMapping[$docType] ?? null;
                    if ($profileField && !empty($student->document->$profileField)) {
                        $sourcePath = $student->document->$profileField;
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($sourcePath)) {
                            // Copy from Profile to Application (Snapshot)
                            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
                            $newPath = 'documents/' . $app->id . '/' . $dbColumn . '_application_' . $app->application_form_id . '_' . $app->student_id . '.' . $extension;
                            
                            try {
                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($newPath)) {
                                    \Illuminate\Support\Facades\Storage::disk('public')->delete($newPath);
                                }
                                \Illuminate\Support\Facades\Storage::disk('public')->copy($sourcePath, $newPath);
                                $updateData[$dbColumn] = $newPath;
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Failed to copy profile document for $dbColumn: " . $e->getMessage());
                            }
                        }
                    }
                }

                // Final check: is document present?
                if (empty($appDoc->$dbColumn) && !in_array($docType, $optionalDocs)) {
                    // Check if it exists in student profile
                    $profileField = $docMapping[$docType] ?? null;
                    if (!$profileField || empty($student->document->$profileField)) {
                        \Illuminate\Support\Facades\Log::warning("AppPreview: Missing mandatory doc $docType");
                        $niceDocLabel = in_array($docType, ['btc_residential_certificate', 'prc_certificate']) ? 'Permanent Resident Certificate (PRC)' : ucfirst(str_replace('_', ' ', $docType));
                        return back()->withInput()->with('error', "Mandatory document missing: " . $niceDocLabel);
                    }
                }
            }

            if (!empty($updateData)) {
                $appDoc->update($updateData);
                \Illuminate\Support\Facades\Log::info("AppPreview: Updated AppDoc with data: " . json_encode($updateData));
            }

            $app = \App\Models\StudentApplication::with(['application_form', 'application_documents'])->find($app->id);

        } else {
            \Illuminate\Support\Facades\Log::warning("AppPreview: App not found for validation.");
        }

        // Exclude files from session data to prevent serialization error
        $dataToStore = $request->except(array_keys($request->file()));
        if ($app) {
            $dataToStore['application_id'] = $app->id;
            $dataToStore['form_id'] = $app->application_form_id;
            $dataToStore['applied_for'] = $data['applied_for'] ?? ($app->applied_for ?? '');
            $dataToStore['examination_center'] = $data['examination_center'] ?? ($app->examination_center ?? '');
        }
        session(['application_data' => $dataToStore]);

        if ($request->boolean('direct_final_submit') && $app) {
            $request->merge([
                'application_id' => $app->id,
                'form_id' => $app->application_form_id,
                'applied_for' => $dataToStore['applied_for'],
                'examination_center' => $dataToStore['examination_center'],
            ]);
            return $this->storeApplication($request);
        }
                
        return view('student.application.preview', compact('data', 'app'));
    }

    public function storeApplication(Request $request)
    {
        $data = session('application_data') ?? [];
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        
        $appId = $request->input('application_id') ?? ($data['application_id'] ?? null);

        // Fallback 1: Lookup by form_id for current student
        if (!$appId) {
            $formId = $request->input('form_id') ?? ($data['form_id'] ?? null);
            if ($formId) {
                $appId = \App\Models\StudentApplication::where('student_id', $student->id)
                    ->where('application_form_id', $formId)
                    ->where('status', 'draft')
                    ->latest()
                    ->value('id');
            }
        }

        // Fallback 2: Lookup latest draft application for current student
        if (!$appId) {
            $appId = \App\Models\StudentApplication::where('student_id', $student->id)
                ->where('status', 'draft')
                ->latest()
                ->value('id');
        }

        // Fallback 3: If no draft exists, check if already submitted recently
        if (!$appId) {
            $latestSubmitted = \App\Models\StudentApplication::where('student_id', $student->id)
                ->whereIn('status', ['submitted', 'pending', 'approved'])
                ->latest()
                ->first();
            if ($latestSubmitted) {
                session()->forget('application_data');
                return redirect()->route('student.applications.history')->with('success', 'Application submitted successfully!');
            }

            return redirect()->route('student.applications.history')->with('error', 'Session expired or application not found. Please try again.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $application = \App\Models\StudentApplication::with('application_form')
                                                        ->where('id', $appId)
                                                        ->where('student_id', $student->id)
                                                        ->firstOrFail();

            // If already submitted (e.g. double submit or rapid clicking), return success gracefully
            if (in_array($application->status, ['submitted', 'pending', 'approved'])) {
                \Illuminate\Support\Facades\DB::rollBack();
                session()->forget('application_data');
                return redirect()->route('student.applications.history')->with('success', 'Application submitted successfully!');
            }

            $form = $application->application_form;
            if ($form->isExpired()) {
                \Illuminate\Support\Facades\DB::rollBack();
                return redirect()->route('student.applications.history')->with('error', 'Application submission failed. The form is no longer active or the deadline has passed.');
            }

            // Just update the status and ensure applied_for and examination_center are locked in
            $appliedForVal = $request->input('applied_for') ?? ($data['applied_for'] ?? $application->applied_for);
            $examinationCenterVal = $request->input('examination_center') ?? ($data['examination_center'] ?? $application->examination_center);
            $updatePayload = [
                'status' => 'submitted',
                'submitted_at' => now(),
            ];
            // Generate official application code only upon final submission
            $updatePayload['application_code'] = \App\Models\StudentApplication::generateApplicationCode($appliedForVal, $application->id);
            if ($appliedForVal && \Illuminate\Support\Facades\Schema::hasColumn('student_applications', 'applied_for')) {
                $updatePayload['applied_for'] = $appliedForVal;
            }
            if ($examinationCenterVal && \Illuminate\Support\Facades\Schema::hasColumn('student_applications', 'examination_center')) {
                $updatePayload['examination_center'] = $examinationCenterVal;
            }
            $application->update($updatePayload);

            $appStudentUpdates = [];
            if ($appliedForVal && \Illuminate\Support\Facades\Schema::hasColumn('application_students', 'applied_for')) {
                $appStudentUpdates['applied_for'] = $appliedForVal;
            }
            if ($examinationCenterVal && \Illuminate\Support\Facades\Schema::hasColumn('application_students', 'examination_center')) {
                $appStudentUpdates['examination_center'] = $examinationCenterVal;
            }
            if (!empty($appStudentUpdates) && $application->application_student) {
                $application->application_student->update($appStudentUpdates);
            }

            \Illuminate\Support\Facades\DB::commit();
            
            // Forget session
            session()->forget('application_data'); 

            // Conditional Redirection: Payment vs History
            $form = $application->application_form;
            $isFeeRequired = ($form->is_application_fee_required && $form->getApplicationFeeFor($student, $application) > 0);

            if ($isFeeRequired) {
                return redirect()->route('student.application.payment', ['id' => $application->id])
                                 ->with('success', 'Application submitted. Please complete the payment to proceed.');
            }

            // Redirect to My Applications List as requested
            return redirect()->route('student.applications.history')->with('success', 'Application submitted successfully!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('student.applications.history')->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    public function saveStep(Request $request)
    {
        $step = $request->input('step');
        $form_id = $request->input('form_id');
        $user = auth()->user();
        
        // 1. Ensure Student Record exists (Just for linking, don't update fields here)
        $student = \App\Models\Student::firstOrCreate(['user_id' => $user->id], [
            'full_name' => $user->name,
            'phone_number' => $user->mobile_number,
            'email' => $user->email,
        ]);

        // 2. Ensure Application Draft exists
        // Check if form is active if creating new
        $form = \App\Models\ApplicationForm::find($form_id);
        if (!$form || $form->status !== 'active') {
             return response()->json(['success' => false, 'message' => 'Form is not active.'], 403);
        }

        if ($form->isDeadlinePassed()) {
             return response()->json(['success' => false, 'message' => 'Application deadline has passed.'], 403);
        }

        $application = \App\Models\StudentApplication::firstOrCreate(
            [
                'student_id' => $student->id,
                'application_form_id' => $form_id,
                'status' => 'draft' 
            ],
            [
                'application_code' => null, // Generated only on final submit
                'payment_status' => 'pending'
            ]
        );

        // Build dynamic validation rules based on field_config
        $rules = [];
        if ($step == 1) {
            // Bidirectional synchronization between community and nationality
            if ($request->filled('community') && !$request->filled('nationality')) {
                $request->merge(['nationality' => $request->community]);
            } elseif ($request->filled('nationality') && !$request->filled('community')) {
                $request->merge(['community' => $request->nationality]);
            }

            $personalFields = [
                'full_name', 'phone_number', 'email', 'gender', 'dob', 
                'category', 'religion', 'age', 
                'blood_group', 'community', 'mother_tongue', 
                'identification_mark', 'alternate_mobile_number',
                'is_permanent_resident_of_assam', 'is_btc_resident',
            ];

            $rules['applied_for'] = 'required|string|max:255';
            $rules['examination_center'] = 'required|string|in:Kokrajhar,Chirang,Baksa,Tamulpur,Udalguri';

            foreach($personalFields as $field) {
                if($form->isFieldVisible('personal', $field)) {
                    if (in_array($field, ['is_permanent_resident_of_assam', 'is_btc_resident'])) {
                        $rules[$field] = 'nullable|boolean';
                    } elseif ($field == 'alternate_mobile_number') {
                        $rules[$field] = 'required|string|max:15';
                    } else {
                        $rules[$field] = 'required';
                        if($field == 'email') $rules[$field] .= '|email';
                        if($field == 'phone_number') $rules[$field] .= '|max:15';
                        if($field == 'age') $rules[$field] .= '|integer|min:14|max:100';
                        if($field == 'dob') $rules[$field] .= '|date';
                    }
                }
            }

            // Parent Details (Part of Step 1)
            $parentFields = [
                'father_name', 'father_mobile', 'father_occupation', 'father_annual_income',
                'mother_name', 'mother_mobile', 'mother_occupation',
                'guardian_name', 'guardian_relation', 'guardian_mobile', 'guardian_address'
            ];
            foreach($parentFields as $field) {
                if($form->isFieldVisible('parents', $field)) {
                    $rules[$field] = 'required';
                }
            }
        }
        elseif ($step == 2) {
            $request->merge(['permanent_state' => 'Assam']);

            $addressFields = [
                'village_town', 'street', 'city', 'district', 'state', 'country', 'pin_code'
            ];

            if ($request->boolean('same_as_permanent')) {
                foreach($addressFields as $field) {
                    if (!$request->filled('present_'.$field) && $request->filled('permanent_'.$field)) {
                        $request->merge(['present_'.$field => $request->input('permanent_'.$field)]);
                    }
                }
            }

            foreach($addressFields as $field) {
                if($form->isFieldVisible('address', 'permanent_'.$field)) {
                    if ($field === 'district') {
                        $rules['permanent_district'] = 'required|in:Kokrajhar,Chirang,Baksa,Tamulpur,Udalguri';
                    } else {
                        $rules['permanent_'.$field] = 'required';
                    }
                }
                if($form->isFieldVisible('address', 'present_'.$field) && !$request->boolean('same_as_permanent')) {
                    $rules['present_'.$field] = 'required';
                }
            }
        }
        elseif ($step == 3) {
            $bankFields = [
                'account_holder_name', 'bank_name', 'branch', 'account_number', 'ifsc_code', 'upi_id'
            ];

            foreach($bankFields as $field) {
                if($form->isFieldVisible('bank', $field)) {
                    $rules[$field] = 'required';
                }
            }
        }
        elseif ($step == 4) {
             // If section is enabled, require at least one row
             if($form->isSectionEnabled('academic')) {
                 $rules['qual'] = 'required|array|min:1';
                 $rules['qual.*.examination'] = 'required|string';
                 $rules['qual.*.passing_year'] = 'required';
                 $rules['qual.*.board_university'] = 'required';
                 $rules['qual.*.percentage_grade'] = 'required';
             }
        }
        
        $messages = [
            'examination_center.required' => 'Please select your preferred Examination Center.',
            'examination_center.in' => 'Examination Center must be one of: Kokrajhar, Chirang, Baksa, Tamulpur, Udalguri.',
            'permanent_district.in' => 'Permanent Address District must be one of: Kokrajhar, Chirang, Baksa, Tamulpur, Udalguri.',
            'qual.required' => 'Please add at least one academic qualification.',
            'qual.*.examination.required' => 'Examination name is required.',
            'qual.*.passing_year.required' => 'Passing year is required.',
            'qual.*.board_university.required' => 'School/College/Institute Name is required.',
            'qual.*.percentage_grade.required' => 'Percentage/Grade is required.',
            'age.min' => 'Minimum age requirement is 14 years.',
            'age.max' => 'Maximum age limit is 100 years.',
        ];

        $attributes = [
            'alternate_mobile_number' => 'What’s App Number',
            'father_annual_income' => 'Family income per (per anum)',
            'permanent_street' => 'BTCLA Constituency',
            'community' => 'Community',
            'nationality' => 'Community',
            'permanent_city' => 'Permanent Post Office',
            'permanent_country' => 'Permanent Police Station',
            'present_city' => 'Present Post Office',
            'present_country' => 'Present Police Station',
            'permanent_district' => 'Permanent Address District',
            'applied_for' => 'Course / Stream Applied For',
            'examination_center' => 'Examination Center',
        ];

        if (!empty($rules)) {
            $request->validate($rules, $messages, $attributes);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            if ($step == 1) {
                // --- Step 1: Personal Details ---
                // Phone number and email cannot change - lock to student's registered account
                $fixedPhone = $student->phone_number ?? auth()->user()->mobile_number ?? $request->phone_number;
                $fixedEmail = $student->email ?? auth()->user()->email ?? $request->email;

                $appStudentData = [
                    'full_name' => $request->full_name,
                    'phone_number' => $fixedPhone,
                    'email' => $fixedEmail,
                    'dob' => $request->dob,
                    'gender' => $request->gender,
                    'category' => $request->category,
                    'religion' => $request->religion,
                    'marital_status' => $request->marital_status,
                    'age' => $request->age,
                    'blood_group' => $request->blood_group,
                    'community' => $request->community ?? $request->nationality,
                    'nationality' => $request->community ?? $request->nationality,
                    'mother_tongue' => $request->mother_tongue,
                    'identification_mark' => $request->identification_mark,
                    'alternate_mobile_number' => $request->alternate_mobile_number,
                    'is_pwd' => $request->has('is_pwd') ? $request->boolean('is_pwd') : null,
                    'employment_exchange_no' => $request->employment_exchange_no,
                    'is_permanent_resident_of_assam' => $request->filled('is_permanent_resident_of_assam') ? $request->boolean('is_permanent_resident_of_assam') : null,
                    'is_btc_resident' => $request->filled('is_btc_resident') ? $request->boolean('is_btc_resident') : null,
                    'is_ex_serviceman' => $request->has('is_ex_serviceman') ? $request->boolean('is_ex_serviceman') : null,
                    'ncc_certificate' => $request->ncc_certificate,
                    'has_national_sports_achievement' => $request->has('has_national_sports_achievement') ? $request->boolean('has_national_sports_achievement') : null,
                    'has_fine_arts_cultural_achievement' => $request->has('has_fine_arts_cultural_achievement') ? $request->boolean('has_fine_arts_cultural_achievement') : null,
                ];

                if ($request->filled('applied_for') && \Illuminate\Support\Facades\Schema::hasColumn('application_students', 'applied_for')) {
                    $appStudentData['applied_for'] = $request->applied_for;
                }
                if ($request->filled('examination_center') && \Illuminate\Support\Facades\Schema::hasColumn('application_students', 'examination_center')) {
                    $appStudentData['examination_center'] = $request->examination_center;
                }

                // Update Application Snapshot ONLY
                \App\Models\ApplicationStudent::updateOrCreate(
                    ['student_application_id' => $application->id],
                    $appStudentData
                );

                $appUpdate = [];
                if ($request->filled('applied_for') && \Illuminate\Support\Facades\Schema::hasColumn('student_applications', 'applied_for')) {
                    $appUpdate['applied_for'] = $request->applied_for;
                }
                if ($request->filled('examination_center') && \Illuminate\Support\Facades\Schema::hasColumn('student_applications', 'examination_center')) {
                    $appUpdate['examination_center'] = $request->examination_center;
                }
                if ($request->filled('applied_for') && \Illuminate\Support\Facades\Schema::hasColumn('student_applications', 'applied_for')) {
                    $appUpdate['applied_for'] = $request->applied_for;
                }
                if ($request->filled('examination_center') && \Illuminate\Support\Facades\Schema::hasColumn('student_applications', 'examination_center')) {
                    $appUpdate['examination_center'] = $request->examination_center;
                }
                if (!empty($appUpdate)) {
                    $application->update($appUpdate);
                }

                $sessionData = session('application_data', []);
                if ($request->filled('applied_for')) {
                    $sessionData['applied_for'] = $request->applied_for;
                }
                if ($request->filled('examination_center')) {
                    $sessionData['examination_center'] = $request->examination_center;
                }
                session(['application_data' => $sessionData]);

                // Update Application Parents Snapshot ONLY
                \App\Models\ApplicationParent::updateOrCreate(
                    ['student_application_id' => $application->id],
                    [
                        'father_name' => $request->father_name,
                        'father_mobile' => $request->father_mobile,
                        'father_occupation' => $request->father_occupation,
                        'father_annual_income' => $request->father_annual_income,
                        'mother_name' => $request->mother_name,
                        'mother_mobile' => $request->mother_mobile,
                        'mother_occupation' => $request->mother_occupation,
                        'guardian_name' => $request->guardian_name,
                        'guardian_relation' => $request->guardian_relation,
                        'guardian_mobile' => $request->guardian_mobile,
                        'guardian_address' => $request->guardian_address,
                        'guardian_occupation' => $request->guardian_occupation,
                    ]
                );
            }
            elseif ($step == 2) {
                // --- Step 2: Address Details ---
                // Already validated above

                // Update Application Address Snapshot ONLY
                $isSame = $request->boolean('same_as_permanent');
                \App\Models\ApplicationAddress::updateOrCreate(
                    ['student_application_id' => $application->id],
                    [
                        'permanent_village_town' => $request->permanent_village_town,
                        'permanent_street' => $request->permanent_street,
                        'permanent_city' => $request->permanent_city,
                        'permanent_district' => $request->permanent_district,
                        'permanent_state' => 'Assam',
                        'permanent_country' => $request->permanent_country,
                        'permanent_pin_code' => $request->permanent_pin_code,
                        'present_village_town' => $isSame ? ($request->permanent_village_town ?? $request->present_village_town) : $request->present_village_town,
                        'present_street' => $isSame ? ($request->permanent_street ?? $request->present_street) : $request->present_street,
                        'present_city' => $isSame ? ($request->permanent_city ?? $request->present_city) : $request->present_city,
                        'present_district' => $isSame ? ($request->permanent_district ?? $request->present_district) : $request->present_district,
                        'present_state' => $isSame ? 'Assam' : ($request->present_state ?? null),
                        'present_country' => $isSame ? ($request->permanent_country ?? $request->present_country) : $request->present_country,
                        'present_pin_code' => $isSame ? ($request->permanent_pin_code ?? $request->present_pin_code) : $request->present_pin_code,
                        'same_as_permanent' => $isSame,
                    ]
                );
            }
            elseif ($step == 3) {
                // --- Step 3: Bank Details ---
                // Already validated above

                // Update Application Bank Snapshot ONLY
                \App\Models\ApplicationBankDetail::updateOrCreate(
                    ['student_application_id' => $application->id],
                    [
                        'account_holder_name' => $request->account_holder_name,
                        'bank_name' => $request->bank_name,
                        'branch' => $request->branch,
                        'account_number' => $request->account_number,
                        'ifsc_code' => $request->ifsc_code,
                        'upi_id' => $request->upi_id,
                    ]
                );
            }
            elseif ($step == 4) {
                 // --- Step 4: Academic Details ---
                 // Already validated above

                // Update Application Academic Snapshot ONLY
                $application->application_academic_qualifications()->delete(); // Clear previous snapshot rows for this app
                if ($request->has('qual') && is_array($request->qual)) {
                    foreach ($request->qual as $q) {
                        // Support both old keys (exam, year) and new keys (examination, passing_year)
                        $data = [];
                        $data['examination'] = $q['examination'] ?? ($q['exam'] ?? null);
                        $data['passing_year'] = $q['passing_year'] ?? ($q['year'] ?? null);
                        $data['board_university'] = $q['board_university'] ?? ($q['board'] ?? null);
                        $data['percentage_grade'] = $q['percentage_grade'] ?? ($q['marks'] ?? null);
                        $data['division'] = $q['division'] ?? ($q['div'] ?? null);
                        
                        // Add other fields if present in request and model
                        foreach(['roll_number', 'registration_number', 'total_marks', 'marks_obtained', 'medium_of_study'] as $extraField) {
                             if(isset($q[$extraField])) {
                                 $data[$extraField] = $q[$extraField];
                             }
                        }

                        if (!empty($data['examination'])) {
                            $application->application_academic_qualifications()->create($data);
                        }
                    }
                }
            }

            elseif ($step == 5) {
                // --- Step 5: Documents ---
                $docConfig = $form->field_config['documents']['fields'] ?? [];
                if (empty($docConfig)) {
                     $docConfig = ['photo', 'caste_certificate', 'signature', 'prc_certificate', 'age_proof', 'mark_sheet_10', 'certificate_10'];
                }
                $docConfig = array_values(array_filter($docConfig, fn($f) => $form->isFieldVisible('documents', $f)));

                $student->load('document'); // Ensure document relation is loaded
                $appDoc = \App\Models\ApplicationDocument::firstOrCreate(['student_application_id' => $application->id]);

                // Map Application Document Types to Student Profile Document Fields
                $profileMap = [
                    'photo' => 'photo',
                    'signature' => 'signature',
                    'caste_certificate' => 'caste_certificate',
                    'mark_sheet_10' => 'hslc_marksheet',
                    'hslc_marksheet' => 'hslc_marksheet',
                    'certificate_10' => 'hslc_pass_certificate',
                    'hslc_pass_certificate' => 'hslc_pass_certificate',
                    'mark_sheet_12' => 'hs_marksheet',
                    'hs_marksheet' => 'hs_marksheet',
                    'certificate_12' => 'hs_pass_certificate',
                    'hs_pass_certificate' => 'hs_pass_certificate',
                    'graduation_marksheet' => 'graduation_marksheet',
                    'graduation_certificate' => 'graduation_pass_certificate',
                    'graduation_pass_certificate' => 'graduation_pass_certificate',
                    'post_graduation_marksheet' => 'post_graduation_marksheet',
                    'post_graduation_certificate' => 'post_graduation_pass_certificate',
                    'post_graduation_pass_certificate' => 'post_graduation_pass_certificate',
                    'prc_certificate' => 'address_proof', 
                    'address_proof' => 'address_proof',
                    'age_proof' => 'hslc_admit_card', 
                    'hslc_admit_card' => 'hslc_admit_card',
                    'hs_admit_card' => 'hs_admit_card',
                    'graduation_admit_card' => 'graduation_admit_card',
                    'post_graduation_admit_card' => 'post_graduation_admit_card',
                    'income_certificate' => 'income_certificate',
                    'aadhaar_card' => 'aadhaar_card',
                    'btc_residential_certificate' => 'btc_residential_certificate',
                ];

                $updateData = [];

                foreach ($docConfig as $docType) {
                    $dbColumn = $profileMap[$docType] ?? $docType;
                    
                    if ($request->hasFile($docType)) {
                        // User uploaded a new file
                        $file = $request->file($docType);
                        $path = $file->store('documents/' . $application->id, 'public');
                        $updateData[$dbColumn] = $path;
                    }
                    elseif (empty($appDoc->$dbColumn)) {
                        // No new file and no existing doc in app snapshot. Check Profile.
                        $profileField = $profileMap[$docType] ?? null;
                        if ($profileField && !empty($student->document->$profileField)) {
                             $sourcePath = $student->document->$profileField;
                             if (\Illuminate\Support\Facades\Storage::disk('public')->exists($sourcePath)) {
                                 // Copy from Profile to Application (Snapshot)
                                 $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
                                 $newPath = 'documents/' . $application->id . '/' . $dbColumn . '_profile_' . time() . '.' . $extension;
                                 
                                 try {
                                     \Illuminate\Support\Facades\Storage::disk('public')->copy($sourcePath, $newPath);
                                     $updateData[$dbColumn] = $newPath;
                                 } catch (\Exception $e) {
                                     \Illuminate\Support\Facades\Log::error("Failed to copy profile document for $dbColumn: " . $e->getMessage());
                                 }
                             }
                        }
                    }

                    // Strict validation: After potential copy, check if we have it
                    $optionalDocs = ['caste_certificate'];
                    
                    if (empty($appDoc->$dbColumn) && empty($updateData[$dbColumn]) && !in_array($docType, $optionalDocs)) {
                        $label = in_array($docType, ['btc_residential_certificate', 'prc_certificate']) ? 'Permanent Resident Certificate (PRC)' : ucwords(str_replace('_', ' ', $docType));
                        return response()->json([
                            'success' => false, 
                            'message' => "The $label is required. Please upload it or update your profile."
                        ], 422);
                    }
                }

                if (!empty($updateData)) {
                    $appDoc->update($updateData);
                }
            }

            // Update progress: move to next step (max 5)
            // Bank details (step 3) is removed from the form page, so move from step 2 directly to step 4
            $newStep = ($step == 2) ? 4 : (($step < 5) ? ($step + 1) : 5);
            if ($application->current_step < $newStep) {
                $application->update(['current_step' => $newStep]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Save Step Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function applicationPayment(Request $request)
    {
        $id = $request->query('id');
        if (!$id) {
            return redirect()->route('student.applications.history')->with('error', 'Invalid application for payment.');
        }

        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        $application = \App\Models\StudentApplication::with('application_form')->findOrFail($id);
        
        if ($application->student_id != $student->id) {
             abort(403, 'Access Denied');
        }

        if ($application->payment_status == 'paid') {
             return redirect()->route('student.applications.history')->with('info', 'Application fee already paid.');
        }

        // Logic to create new order if needed
        $razorpayKey = config('services.razorpay.key');
        $razorpaySecret = config('services.razorpay.secret');
        
        // Ensure keys are set
        if (!$razorpayKey || !$razorpaySecret) {
            // Log error or show maintenance mode
            \Illuminate\Support\Facades\Log::error('Razorpay keys missing in config.');
            return back()->with('error', 'Payment gateway configuration missing. Please contact support.');
        }

        // Check for existing valid pending order
        $pendingOrder = $application->latest_payment_order; 

        // Determine if we need a new order
        $needsNewOrder = true;
        if ($pendingOrder && $pendingOrder->status == 'created') {
             // Check if amount matches (in case fee changed)
             if ($pendingOrder->amount == $application->application_form->getApplicationFeeFor($student, $application)) {
                 $needsNewOrder = false;
             }
        }

        if ($needsNewOrder) {
             $amountInPaise = intval($application->application_form->getApplicationFeeFor($student, $application) * 100);
             // Receipt ID must be max 40 chars. Using ID + timestamp is safe.
             $receiptId = 'rcpt_' . $application->id . '_' . time(); 
             
             // Log payload for debugging
             $payload = [
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => $receiptId,
                'notes' => [
                    'application_id' => (string) $application->id,
                    'student_id' => (string) $student->id,
                ]
             ];
             \Illuminate\Support\Facades\Log::info('Razorpay Order Request:', $payload);

             $apiAuth = base64_encode($razorpayKey . ':' . $razorpaySecret);

             try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Basic ' . $apiAuth,
                    'Content-Type' => 'application/json'
                ])->post('https://api.razorpay.com/v1/orders', $payload); // Use defined payload

                if ($response->successful()) {
                    $orderData = $response->json();
                    
                    $pendingOrder = \App\Models\PaymentOrder::create([
                        'student_application_id' => $application->id,
                        'razorpay_order_id' => $orderData['id'],
                        'amount' => $orderData['amount'] / 100, // Store in Rupees
                        'amount_due' => $orderData['amount_due'] / 100,
                        'amount_paid' => $orderData['amount_paid'] / 100,
                        'currency' => $orderData['currency'],
                        'receipt' => $orderData['receipt'],
                        'status' => $orderData['status'],
                        'attempts' => $orderData['attempts'],
                        'notes' => $orderData['notes'],
                        'json_response' => json_encode($orderData),
                    ]);
                } else {
                    \Illuminate\Support\Facades\Log::error('Razorpay Order Creation Failed: ' . $response->body());
                    return back()->with('error', 'Unable to initiate payment gateway. Server returned: ' . $response->status());
                }
             } catch (\Exception $e) {
                 \Illuminate\Support\Facades\Log::error('Razorpay Connection Failed: ' . $e->getMessage());
                 return back()->with('error', 'Payment service unavailable. Please check your connection.');
             }
        }

        return view('student.application.payment', compact('application', 'student', 'pendingOrder', 'razorpayKey'));
    }

    public function processPayment(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all() + $request->query(), [
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
            'application_id' => 'required|exists:student_applications,id',
        ]);

        if ($validator->fails()) {
            return response()->make('
            <!DOCTYPE html>
            <html>
            <head>
                <script>
                    window.location.href = "'.route('student.applications.history', ['payment_error' => 1]).'";
                </script>
            </head>
            <body>
                <p style="font-family: sans-serif; text-align: center; margin-top: 50px;">Invalid payment callback parameters. Redirecting back...</p>
            </body>
            </html>
            ');
        }

        $applicationId = $request->input('application_id') ?? $request->query('application_id');
        $application = \App\Models\StudentApplication::findOrFail($applicationId);
        $student = $application->student;

        // Verify Signature
        $razorpayKey = config('services.razorpay.key');
        $razorpaySecret = config('services.razorpay.secret');
        
        $generatedSignature = hash_hmac('sha256', $request->razorpay_order_id . '|' . $request->razorpay_payment_id, $razorpaySecret);

        if ($generatedSignature == $request->razorpay_signature) {
             
             // 1. Update Payment Order Status
             $paymentOrder = \App\Models\PaymentOrder::where('razorpay_order_id', $request->razorpay_order_id)->first();
             if($paymentOrder) {
                 $paymentOrder->update([
                     'status' => 'paid', 
                     'amount_paid' => $paymentOrder->amount,
                     'amount_due' => 0
                 ]);
             }

             // 2. Create Payment Transaction
             try {
                // Ideally fetch from Razorpay API to get full details, but for now log basic
                // We will fetch full details asynchronously or via another call
                 // Fetching details from API for full record
                 $apiAuth = base64_encode($razorpayKey . ':' . $razorpaySecret);
                 $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Basic ' . $apiAuth,
                 ])->get('https://api.razorpay.com/v1/payments/' . $request->razorpay_payment_id);
                 
                 $paymentData = $response->successful() ? $response->json() : [];

                 $jsonResponse = array_merge($paymentData, [
                      'razorpay_signature' => $request->razorpay_signature
                  ]);

                  \App\Models\PaymentTransaction::updateOrCreate(
                     ['razorpay_payment_id' => $request->razorpay_payment_id],
                     [
                         'payment_order_id' => $paymentOrder->id ?? null,
                         'student_application_id' => $application->id,
                         'amount' => ($paymentData['amount'] ?? 0) / 100,
                         'currency' => $paymentData['currency'] ?? 'INR',
                         'status' => $paymentData['status'] ?? 'captured',
                         'method' => $paymentData['method'] ?? null,
                         'bank' => $paymentData['bank'] ?? null,
                         'wallet' => $paymentData['wallet'] ?? null,
                         'vpa' => $paymentData['vpa'] ?? null,
                         'email' => $paymentData['email'] ?? null,
                         'contact' => $paymentData['contact'] ?? null,
                         'fee' => ($paymentData['fee'] ?? 0) / 100,
                         'tax' => ($paymentData['tax'] ?? 0) / 100,
                         'json_response' => $jsonResponse,
                     ]
                  );

             } catch (\Exception $e) {
                 \Illuminate\Support\Facades\Log::error('Payment Transaction Log Error: ' . $e->getMessage());
             }

             // 3. Update Application to Paid
             $application->update([
                'payment_status' => 'paid',
                'transaction_id' => $request->razorpay_payment_id,
                'status' => 'submitted', 
            ]);

            return response()->make('
            <!DOCTYPE html>
            <html>
            <head>
                <script>
                    window.location.href = "'.route('student.applications.history', ['payment_success' => 1]).'";
                </script>
            </head>
            <body>
                <p style="font-family: sans-serif; text-align: center; margin-top: 50px;">Payment successful! Redirecting you back to your applications list...</p>
            </body>
            </html>
            ');

        } else {
            return response()->make('
            <!DOCTYPE html>
            <html>
            <head>
                <script>
                    window.location.href = "'.route('student.applications.history', ['payment_error' => 1]).'";
                </script>
            </head>
            <body>
                <p style="font-family: sans-serif; text-align: center; margin-top: 50px;">Payment verification failed! Redirecting you back to your applications list...</p>
            </body>
            </html>
            ');
        }
    }

    public function paymentFailure(Request $request) {

        $request->validate([
            'payment_id' => 'required',
            'order_id' => 'required',
            'error_code' => 'nullable',
            'error_description' => 'nullable',
            'application_id' => 'required|exists:student_applications,id',
        ]);

        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        $application = \App\Models\StudentApplication::findOrFail($request->application_id);

        if ($application->student_id != $student->id) {
             abort(403, 'Access Denied');
        }

        $paymentOrder = \App\Models\PaymentOrder::where('razorpay_order_id', $request->order_id)->first();
        
        // Log Failure
        \App\Models\PaymentTransaction::updateOrCreate(
            ['razorpay_payment_id' => $request->payment_id],
            [
                'payment_order_id' => $paymentOrder->id ?? null,
                'student_application_id' => $application->id,
                'amount' => 0, 
                'currency' => 'INR',
                'status' => 'failed',
                'error_code' => $request->error_code,
                'error_description' => $request->error_description,
                'json_response' => $request->all(),
            ]
        );

        return response()->json(['status' => 'logged']);
    }

    public function refreshPaymentStatus(Request $request) {
        $request->validate([
            'application_id' => 'required|exists:student_applications,id',
        ]);

        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        $application = \App\Models\StudentApplication::findOrFail($request->application_id);

        if ($application->student_id != $student->id) {
             abort(403, 'Access Denied');
        }

        $pendingOrder = $application->latest_payment_order;
        if (!$pendingOrder) {
            return back()->with('error', 'No pending payment order found.');
        }

        $razorpayKey = config('services.razorpay.key');
        $razorpaySecret = config('services.razorpay.secret');
        $apiAuth = base64_encode($razorpayKey . ':' . $razorpaySecret);

        // Fetch Order Status from Razorpay
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Basic ' . $apiAuth,
            ])->get('https://api.razorpay.com/v1/orders/' . $pendingOrder->razorpay_order_id . '/payments');


            if ($response->successful()) {
                $payments = $response->json()['items'] ?? [];
                foreach ($payments as $payment) {
                    if ($payment['status'] == 'captured') {
                        // Success! Update everything
                        $pendingOrder->update([
                            'status' => 'paid', 
                            'amount_paid' => $pendingOrder->amount,
                            'amount_due' => 0
                        ]);

                        $application->update([
                            'payment_status' => 'paid',
                            'transaction_id' => $payment['id'],
                            'status' => 'submitted', 
                        ]);

                        // Ensure transaction is logged and updated if it was previously failed
                        \App\Models\PaymentTransaction::updateOrCreate(
                            ['razorpay_payment_id' => $payment['id']],
                            [
                                'payment_order_id' => $pendingOrder->id,
                                'student_application_id' => $application->id,
                                'amount' => ($payment['amount'] ?? 0) / 100,
                                'currency' => $payment['currency'] ?? 'INR',
                                'status' => $payment['status'],
                                'method' => $payment['method'] ?? null,
                                'json_response' => $payment,
                            ]
                        );

                        return redirect()->route('student.applications.history')->with('success', 'Payment verified successfully!');
                    } elseif ($payment['status'] == 'failed') {
                         // Also update/log failed attempts found on server
                         \App\Models\PaymentTransaction::updateOrCreate(
                            ['razorpay_payment_id' => $payment['id']],
                            [
                                'payment_order_id' => $pendingOrder->id,
                                'student_application_id' => $application->id,
                                'amount' => ($payment['amount'] ?? 0) / 100,
                                'currency' => $payment['currency'] ?? 'INR',
                                'status' => $payment['status'],
                                'method' => $payment['method'] ?? null,
                                'error_description' => $payment['error_description'] ?? null, 
                                'json_response' => $payment,
                            ]
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Razorpay Refresh Error: ' . $e->getMessage());
        }

        return back()->with('warning', 'Payment is still pending. If you paid, please wait a few minutes and try again.');
    }




    public function documents()
    {
        $user = auth()->user();
        $student = \App\Models\Student::with(['document', 'otherDocuments'])->where('user_id', $user->id)->firstOrFail();
        
        // Calculate Storage Usage from Standard Documents
        $totalSize = 0;
        $documents = [];
        $limit = 20 * 1024 * 1024; // 20 MB

        if ($student->document) {
            $fillable = $student->document->getFillable();
            foreach ($fillable as $field) {
                if ($field != 'student_id' && !empty($student->document->$field)) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($student->document->$field)) {
                        $size = \Illuminate\Support\Facades\Storage::disk('public')->size($student->document->$field);
                        $totalSize += $size;
                        $documents[$field] = [
                            'path' => $student->document->$field,
                            'size' => $size,
                            'name' => basename($student->document->$field)
                        ];
                    }
                }
            }
        }

        // Calculate Storage Usage from Other Documents
        $otherDocuments = $student->otherDocuments;
        foreach ($otherDocuments as $doc) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->file_path)) {
                $totalSize += $doc->file_size;
            }
        }

        $usagePercentage = ($totalSize / $limit) * 100;

        return view('student.documents', compact('student', 'documents', 'otherDocuments', 'totalSize', 'limit', 'usagePercentage'));
    }

    public function storeDocument(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:200', // 200 KB Max
        ]);

        $user = auth()->user();
        $student = \App\Models\Student::firstOrCreate(['user_id' => $user->id]);
        
        // Check allowed types (columns in StudentDocument)
        $docModel = new \App\Models\StudentDocument();
        $allowedTypes = collect($docModel->getFillable())->reject(fn($item) => $item == 'student_id')->values()->toArray();

        if (!in_array($request->type, $allowedTypes)) {
            return back()->with('error', 'Invalid document type.');
        }

        // Calculate Current Total Storage
        $currentTotalSize = $this->calculateTotalStorage($student);

        $newFileSize = $request->file('file')->getSize();
        $limit = 20 * 1024 * 1024; // 20 MB

        // If replacing, subtract old file size
        $studentDoc = \App\Models\StudentDocument::firstOrCreate(['student_id' => $student->id]);
        if (!empty($studentDoc->{$request->type}) && \Illuminate\Support\Facades\Storage::disk('public')->exists($studentDoc->{$request->type})) {
            $currentTotalSize -= \Illuminate\Support\Facades\Storage::disk('public')->size($studentDoc->{$request->type});
        }

        if (($currentTotalSize + $newFileSize) > $limit) {
            return back()->with('error', 'Storage limit exceeded (20 MB total). Please delete some files first.');
        }

        // Store File
        $path = $request->file('file')->store('student_docs/' . $student->id, 'public');
        
        // Update Record
        $studentDoc->{$request->type} = $path;
        $studentDoc->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully!',
                'path' => $path,
                'type' => $request->type
            ]);
        }

        return back()->with('success', 'Document uploaded successfully!');
    }

    public function deleteDocument($type)
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        $studentDoc = \App\Models\StudentDocument::where('student_id', $student->id)->firstOrFail();

        // Check if type exists and is allowed
        $allowedTypes = collect($studentDoc->getFillable())->reject(fn($item) => $item == 'student_id')->values()->toArray();
        if (!in_array($type, $allowedTypes)) {
            return back()->with('error', 'Invalid document type.');
        }

        if (!empty($studentDoc->$type)) {
            // Delete file from storage
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($studentDoc->$type)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($studentDoc->$type);
            }
            // Update record
            $studentDoc->$type = null;
            $studentDoc->save();
        }

        return back()->with('success', 'Document deleted successfully!');
    }

    public function storeOtherDocument(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:200', // 200 KB Max
        ]);

        $user = auth()->user();
        $student = \App\Models\Student::firstOrCreate(['user_id' => $user->id]);

        // Calculate Current Total Storage
        $currentTotalSize = $this->calculateTotalStorage($student);

        $newFileSize = $request->file('file')->getSize();
        $limit = 20 * 1024 * 1024; // 20 MB

        if (($currentTotalSize + $newFileSize) > $limit) {
            return back()->with('error', 'Storage limit exceeded (20 MB total). Please delete some files first.');
        }

        // Store File
        $path = $request->file('file')->store('student_docs_other/' . $student->id, 'public');

        // Create Record
        \App\Models\StudentOtherDocument::create([
            'student_id' => $student->id,
            'title' => $request->title,
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $newFileSize,
            'mime_type' => $request->file('file')->getMimeType(),
        ]);

        return back()->with('success', 'Other document uploaded successfully!');
    }

    public function deleteOtherDocument($id)
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        $doc = \App\Models\StudentOtherDocument::where('student_id', $student->id)->where('id', $id)->firstOrFail();

        // Delete file
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
        }

        // Delete record
        $doc->delete();

        return back()->with('success', 'Document deleted successfully!');
    }

    private function calculateTotalStorage($student)
    {
        $totalSize = 0;

        // Standard Documents
        if ($student->document) {
            $fillable = $student->document->getFillable();
            foreach ($fillable as $field) {
                if ($field != 'student_id' && !empty($student->document->$field)) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($student->document->$field)) {
                        $totalSize += \Illuminate\Support\Facades\Storage::disk('public')->size($student->document->$field);
                    }
                }
            }
        }

        // Other Documents
        $otherDocuments = \App\Models\StudentOtherDocument::where('student_id', $student->id)->get();
        foreach ($otherDocuments as $doc) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->file_path)) {
                $totalSize += $doc->file_size;
            }
        }

        return $totalSize;
    }


    public function tools()
    {
        return view('student.tools');
    }

    public function initiateEmiPayment(Request $request, $id)
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        
        $application = \App\Models\StudentApplication::with(['application_form.institution', 'application_student'])
            ->where('student_id', $student->id)
            ->findOrFail($id);

        $admission = AdmissionStudent::where('student_application_id', $application->id)->firstOrFail();

        if ($admission->pending_amount <= 0) {
            return response()->json(['success' => false, 'error' => 'No pending amount left to pay.'], 400);
        }

        $institution = $application->application_form->institution;

        // The amount to pay is either the fixed emi_amount, or whatever is pending if pending is less than emi_amount
        $amountToPay = $admission->emi_amount > 0 ? min($admission->emi_amount, $admission->pending_amount) : $admission->pending_amount;

        try {
            DB::beginTransaction();

            $receipt = "EMI_ORD_" . time() . '_' . rand(100, 999);
            $notes = [
               'application_code' => $application->application_code,
               'student_name' => $application->application_student->full_name,
               'payment_type' => 'emi_installment'
            ];

            $orderResponse = $this->razorpayService->createOrder($institution, $amountToPay, $receipt, $notes);

            if (!$orderResponse['success']) {
                throw new \Exception($orderResponse['error']);
            }

            AdmissionPaymentOrder::create([
                'student_application_id' => $application->id,
                'application_form_id' => $application->application_form_id,
                'student_id' => $application->student_id,
                'institution_id' => $institution->id,
                'razorpay_order_id' => $orderResponse['order_id'],
                'amount' => $amountToPay,
                'currency' => 'INR',
                'status' => 'pending',
                'payment_category' => 'emi_installment',
                'user_id' => $user->id,
                'receipt' => $receipt,
                'notes' => $notes
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $orderResponse['order_id'],
                'amount' => $orderResponse['amount'],
                'key' => $institution->razorpay_key,
                'name' => $institution->institution_name,
                'email' => $application->application_student->email ?? '',
                'contact' => $application->application_student->phone_number ?? '',
                'callback_url' => route('student.application.payment.emi.callback', $application->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EMI Payment Initiation Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Could not initiate payment: ' . $e->getMessage()], 500);
        }
    }

    public function handleEmiPaymentCallback(Request $request, $id)
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        
        $application = \App\Models\StudentApplication::with('application_form.institution')->findOrFail($id);

        if ($application->student_id != $student->id) {
             abort(403, 'Access Denied');
        }

        $institution = $application->application_form->institution;

        $razorpayOrderId = $request->razorpay_order_id;
        $razorpayPaymentId = $request->razorpay_payment_id;
        $razorpaySignature = $request->razorpay_signature;

        $attributes = [
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_signature' => $razorpaySignature
        ];

        $isValid = $this->razorpayService->verifySignature($institution, $attributes);

        if (!$isValid) {
            $order = AdmissionPaymentOrder::where('razorpay_order_id', $razorpayOrderId)->first();
            
            AdmissionPaymentTransaction::create([
                'admission_payment_order_id' => $order->id ?? null,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
                'amount' => 0,
                'currency' => 'INR',
                'status' => 'verification_failed',
                'json_response' => $request->all()
            ]);

            if ($order) {
                $order->update(['status' => 'failed']);
            }

            return redirect()->route('student.dashboard')->with('error', 'Payment verification failed (Invalid Signature).');
        }

        try {
            DB::beginTransaction();

            $order = AdmissionPaymentOrder::where('razorpay_order_id', $razorpayOrderId)->firstOrFail();
            
            $api = $this->razorpayService->getApi($institution);
            $payment = $api->payment->fetch($razorpayPaymentId);

            AdmissionPaymentTransaction::create([
                'admission_payment_order_id' => $order->id,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
                'amount' => $payment->amount / 100,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'method' => $payment->method,
                'bank' => $payment->bank,
                'wallet' => $payment->wallet,
                'vpa' => $payment->vpa,
                'email' => $payment->email,
                'contact' => $payment->contact,
                'fee' => $payment->fee / 100,
                'tax' => $payment->tax / 100,
                'json_response' => $payment->toArray()
            ]);

            $order->update(['status' => 'paid']);

            // Record the specific EMI payment
            $admissionPayment = AdmissionPayment::create([
                'student_application_id' => $application->id,
                'payment_type' => 'emi',
                'total_amount' => $application->application_form->course_fee ?? 0, 
                'discount_value' => 0, 
                'discount_type' => 'fixed',
                'final_amount' => $application->application_form->course_fee ?? 0, 
                'paid_amount' => $order->amount,
                'emi_months' => null, 
                'payer_name' => $user->name,
                'payer_phone' => $user->mobile_number,
                'receiver_name' => 'Online Portal',
                'received_by' => null, 
                'status' => 'completed',
                'transaction_id' => $razorpayPaymentId,
                'remarks' => 'Online EMI Installment Payment',
            ]);

            $order->update(['admission_payment_id' => $admissionPayment->id]);

            // Track within Admission Student
            $admission = AdmissionStudent::where('student_application_id', $application->id)->firstOrFail();
            $admission->total_paid_amount += $order->amount;
            $admission->pending_amount = max(0, $admission->total_payable_amount - $admission->total_paid_amount);
            
            if ($admission->pending_amount <= 0) {
                $admission->status = 'completed';
                $admission->next_payment_date = null;
            } else {
                // Determine next month date handling edge cases with days
                $nextDate = \Carbon\Carbon::parse($admission->next_payment_date ?? now());
                $admission->next_payment_date = $nextDate->addMonth();
            }

            $admission->save();

            DB::commit();

            return redirect()->route('student.dashboard')->with('success', 'EMI installment of ₹' . number_format($order->amount, 2) . ' paid successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("EMI Callback processing failed: " . $e->getMessage());
            return redirect()->route('student.dashboard')->with('error', 'Error finalizing payment: ' . $e->getMessage());
        }
    }

    public function emiPaymentFailure(Request $request, $id)
    {
        $request->validate([
            'payment_id' => 'nullable',
            'order_id' => 'required',
            'error_code' => 'nullable',
            'error_description' => 'nullable',
        ]);

        $order = AdmissionPaymentOrder::where('razorpay_order_id', $request->order_id)->first();

        if ($order) {
            $order->update(['status' => 'failed']);
        }

        AdmissionPaymentTransaction::updateOrCreate(
            ['razorpay_payment_id' => $request->payment_id ?? 'failed_' . uniqid()],
            [
                'admission_payment_order_id' => $order->id ?? null,
                'amount' => 0,
                'currency' => 'INR',
                'status' => 'failed',
                'error_code' => $request->error_code,
                'error_description' => $request->error_description,
                'json_response' => $request->all(),
            ]
        );

        return response()->json(['status' => 'logged']);
    }
}
