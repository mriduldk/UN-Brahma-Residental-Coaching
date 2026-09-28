<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\StudentApplication;
use App\Models\ApplicationForm;
use App\Models\AdmissionPayment;
use App\Models\AdmissionPaymentOrder;
use App\Models\AdmissionPaymentTransaction;
use App\Models\AdmissionStudent;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class AdmissionsController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        // Use query param or fallback to session
        $institutionId = $request->query('institution_id') ?? session('last_institution_id');
        
        // --- DIFFERENT logic for Institute: Only show mapped institutions ---
        $institutions = $user->institutions()->latest()->get();
        
        // Use first institution if none selected or invalid
        $selectedInstitution = null;
        if ($institutionId && $institutions->isNotEmpty()) {
             $selectedInstitution = $institutions->where('id', $institutionId)->first();
             // Security check: if explicit ID passed but not in user's list
             if ($request->has('institution_id') && !$selectedInstitution) {
                 abort(403);
             }
        }
        
        if (!$selectedInstitution && $institutions->isNotEmpty()) {
            $selectedInstitution = $institutions->first();
        }

        // --- NEW: Fetch Application Forms ---
        $applicationForms = collect([]);
        $selectedFormId = $request->query('application_form_id') ?? session('last_application_form_id');

        if ($selectedInstitution) {
            $applicationForms = ApplicationForm::where('institution_id', $selectedInstitution->id)
                ->where('status', 'active')
                ->latest()
                ->get();
            
            if ($selectedFormId && !$applicationForms->find($selectedFormId)) {
                $selectedFormId = null;
            }
            session(['last_application_form_id' => $selectedFormId]);
        }

        // --- NEW: Fetch Admissions (Sessions) ---
        $admissions = collect([]);
        $selectedAdmission = null;

        if ($selectedInstitution) {
            // Persist institution selection
            session(['last_institution_id' => $selectedInstitution->id]);

            $admissionsQuery = \App\Models\Admission::where('institution_id', $selectedInstitution->id)
                ->where('status', 'active');
            
            if ($selectedFormId) {
                $admissionsQuery->where('application_form_id', $selectedFormId);
            }

            $admissions = $admissionsQuery->latest()->get();

            // Use query param or fallback to session
            $admissionId = $request->query('admission_id') ?? session('last_admission_id');

            if ($admissionId) {
                $selectedAdmission = $admissions->where('id', $admissionId)->first();
            }

            // Default to latest active, or just latest
            if (!$selectedAdmission && $admissions->isNotEmpty()) {
                $selectedAdmission = $admissions->where('status', 'active')->first() ?? $admissions->first();
            }

            // Persist admission selection if we have one
            if ($selectedAdmission) {
                session(['last_admission_id' => $selectedAdmission->id]);
            }
        }

        // Stats Logic
        $stats = [
            'total_admissions' => 0,
            'total_revenue' => 0,
            'unsettled_cash' => 0,
            'full_payment' => 0,
            'emi_payment' => 0,
        ];

        if ($selectedInstitution) {
            $applicationsQuery = StudentApplication::with(['application_form', 'application_student', 'admission_student', 'admission_payments'])
                ->whereHas('application_form', function($q) use ($selectedInstitution) {
                    $q->where('institution_id', $selectedInstitution->id);
                });

            if ($selectedAdmission) {
                // If Admission Config is linked to a specific Form, filter by that Form
                if ($selectedAdmission->application_form_id) {
                     $applicationsQuery->where('application_form_id', $selectedAdmission->application_form_id);
                } else {
                    // Fallback to Session match
                    $applicationsQuery->whereHas('application_form', function($q) use ($selectedAdmission) {
                        $q->where('session', $selectedAdmission->academic_session);
                    });
                }
            }

            // Search Filter
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $applicationsQuery->where(function($q) use ($search) {
                    $q->where('application_code', 'like', "%{$search}%")
                      ->orWhereHas('application_student', function($qs) use ($search) {
                          $qs->where('full_name', 'like', "%{$search}%")
                             ->orWhere('phone_number', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            $applications = $applicationsQuery->latest()->paginate(5)->withQueryString();

            // Calculate Stats for the SELECTED institution & session
            $statsData = AdmissionStudent::where('institution_id', $selectedInstitution->id);
            
            if ($selectedFormId) {
                $statsData->where('application_form_id', $selectedFormId);
            }

            if ($selectedAdmission) {
                // If Admission Config is linked to a specific Form, it's already filtered by form_id above.
                // We add session filter to narrow down to that specific config's session.
                $statsData->where('academic_session', $selectedAdmission->academic_session);
            }
            
            $admissionsList = $statsData->with(['admission_payments'])->get();
            $stats['total_admissions'] = $admissionsList->count();
            
            $now = now();
            $nextWeek = now()->addDays(7);
            $stats['total_pending_emi'] = 0;
            $stats['upcoming_dues_count'] = 0;

            foreach($admissionsList as $adm) {
                $stats['total_revenue'] += $adm->total_paid_amount;
                if ($adm->payment_type == 'emi') {
                    $stats['emi_payment']++;
                    $stats['total_pending_emi'] += $adm->pending_amount;

                    if ($adm->next_payment_date && $adm->pending_amount > 0) {
                        $dueDate = \Carbon\Carbon::parse($adm->next_payment_date);
                        if ($dueDate->lessThanOrEqualTo($nextWeek)) {
                            $stats['upcoming_dues_count']++;
                        }
                    }
                } else {
                    $stats['full_payment']++;
                }
                
                $stats['unsettled_cash'] += $adm->admission_payments
                    ->where('payment_type', 'cash')
                    ->where('is_settled', false)
                    ->sum('paid_amount');
            }
        } else {
            $applications = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
        }
        
        return view('institute.admissions.index', compact('institutions', 'selectedInstitution', 'applicationForms', 'selectedFormId', 'admissions', 'selectedAdmission', 'applications', 'stats'));
    }

    public function show($id)
    {
        $application = StudentApplication::with([
            'application_form',
            'application_student',
            'application_address',
            'application_parent',
            'application_bank_detail',
            'application_academic_qualifications',
            'application_documents',
            'payment_transactions',
            'admission_payments',
            'admission_student'
        ])->findOrFail($id);

        // Security check: Ensure application belongs to an institution mapped to the user
        $user = auth()->user();
        $institutionId = $application->application_form->institution_id;
        
        if (!$institutionId || !$user->institutions()->where('institutions.id', $institutionId)->exists()) {
             abort(403, 'Access Denied: You are not authorized to view applications for this institution.');
        }

        // Fetch Admission Config
        // 1. Try to find config specifically for this Application Form
        $admissionConfig = \App\Models\Admission::where('application_form_id', $application->application_form_id)
            ->where('status', 'active')
            ->first();
        
        // 2. Fallback: Match by Institution and Session
        // if (!$admissionConfig) {
        //      $admissionConfig = \App\Models\Admission::where('institution_id', $institutionId)
        //         ->where('academic_session', $application->application_form->session)
        //         ->where('status', 'active')
        //         ->latest()
        //         ->first();
        // }

        // 3. Last fallback (optional, maybe unsafe if fees vary widely)
        // if (!$admissionConfig) {
        //      $admissionConfig = \App\Models\Admission::where('institution_id', $institutionId)
        //         ->where('status', 'active')
        //         ->latest()
        //         ->first();
        // }
        
        return view('institute.admissions.show', compact('application', 'admissionConfig'));
    }

    public function storePayment(Request $request, $id)
    {
        $application = StudentApplication::with('application_form', 'application_student')->findOrFail($id);
        $institution = $application->application_form->institution;
        
        // Security check
        $user = auth()->user();
        if (!$institution || !$user->institutions()->where('institutions.id', $institution->id)->exists()) {
             abort(403, 'Access Denied');
        }

        $validator = Validator::make($request->all(), [
        'payment_type' => 'required|in:full,emi,cash',
        'amount_paid' => 'required|numeric|min:1',
        'discount_value' => 'nullable|numeric|min:0',
        'discount_type' => 'nullable|in:percentage,fixed',
        'emi_months' => 'nullable|integer|min:1',
        'payer_name' => 'nullable|string|required_if:payment_type,cash',
        'payer_phone' => 'nullable|string|required_if:payment_type,cash',
    ]);

    if ($validator->fails()) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'error' => implode(', ', $validator->errors()->all())], 422);
        }
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $validated = $validator->validated();

    $admission = AdmissionStudent::where('student_application_id', $application->id)->first();
    
    // If already admitted, use existing payable amount
    if ($admission) {
        $finalAmount = $admission->total_payable_amount;
    } else {
        $courseFee = $application->application_form->course_fee ?? 30000;
        
        // Calculate Discounted Total (Final Amount)
        $discountAmount = 0;
        if ($request->discount_value > 0) {
            if ($request->discount_type == 'percentage') {
                $discountAmount = ($courseFee * $request->discount_value) / 100;
            } else {
                $discountAmount = $request->discount_value;
            }
        }
        $finalAmount = $courseFee - $discountAmount;
    }

    // Additional validation for EMI type
    if ($request->payment_type == 'emi' && !$request->emi_months && (!$admission || !$admission->emi_months)) {
         if ($request->ajax()) {
            return response()->json(['success' => false, 'error' => 'The emi months field is required.'], 422);
        }
        return redirect()->back()->with('error', 'EMI tenure is required.');
    }

        // --- Handle Cash Payment ---
        if ($request->payment_type == 'cash') {
            return $this->finalizeAdmissionPayment($application, $request->all(), $finalAmount, 'completed');
        }

        // --- Handle Online Payment (Full/EMI) ---
        try {
            DB::beginTransaction();

            // 1. Create Razorpay Order FIRST to get the ID
            $receipt = "ADM_ORD_" . time(); // Temporary receipt until we have record ID
            $notes = [
               'application_code' => $application->application_code,
               'student_name' => $application->application_student->full_name,
               'payment_type' => $request->payment_type
            ];

            $orderResponse = $this->razorpayService->createOrder($institution, $request->amount_paid, $receipt, $notes);

            if (!$orderResponse['success']) {
                throw new \Exception($orderResponse['error']);
            }

            // 2. Create Internal Order Record with Razorpay Order ID
            $orderRecord = AdmissionPaymentOrder::create([
                'student_application_id' => $application->id,
                'application_form_id' => $application->application_form_id,
                'student_id' => $application->student_id,
                'institution_id' => $institution->id,
                'razorpay_order_id' => $orderResponse['order_id'],
                'amount' => $request->amount_paid,
                'currency' => 'INR',
                'status' => 'pending',
                'payment_category' => $request->payment_type,
                'user_id' => $user->id,
                'receipt' => $receipt,
                'notes' => [
                    'discount_value' => $request->discount_value,
                    'discount_type' => $request->discount_type,
                    'emi_months' => $request->emi_months,
                    'final_total' => $finalAmount
                ]
            ]);

            DB::commit();

            // Return JSON if AJAX, or handle appropriately
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $orderResponse['order_id'],
                    'amount' => $orderResponse['amount'],
                    'key' => $institution->razorpay_key,
                    'name' => $institution->institution_name,
                    'email' => $application->application_student->email ?? '',
                    'contact' => $application->application_student->phone_number ?? '',
                    'callback_url' => route('institute.admissions.payment.callback', $application->id)
                ]);
            }

            // Fallback: If not AJAX (should not happen with new UI)
            return response()->json(['error' => 'AJAX request expected'], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', 'Could not initiate payment: ' . $e->getMessage());
        }
    }

    public function handlePaymentCallback(Request $request, $id)
    {
        $application = StudentApplication::with('application_form.institution')->findOrFail($id);
        $institution = $application->application_form->institution;

        $razorpayOrderId = $request->razorpay_order_id;
        $razorpayPaymentId = $request->razorpay_payment_id;
        $razorpaySignature = $request->razorpay_signature;

        // 1. Verify Signature
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

            return redirect()->route('institute.admissions.show', $id)->with('error', 'Payment verification failed (Invalid Signature).');
        }

        // 2. Process Success
        try {
            DB::beginTransaction();

            $order = AdmissionPaymentOrder::where('razorpay_order_id', $razorpayOrderId)->firstOrFail();
            
            // 2.1 Fetch Payment Details from Razorpay for better record keeping
            $api = $this->razorpayService->getApi($institution);
            $payment = $api->payment->fetch($razorpayPaymentId);

            // Record Transaction
            AdmissionPaymentTransaction::create([
                'admission_payment_order_id' => $order->id,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
                'amount' => $payment->amount / 100, // convert back from paise
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

            // --- Handle Settlement Online ---
            if ($order->payment_category == 'settlement') {
                $paymentRecord = AdmissionPayment::find($order->admission_payment_id);
                if ($paymentRecord) {
                    $paymentRecord->update([
                        'is_settled' => true,
                        'settled_at' => now(),
                        'transaction_id' => $razorpayPaymentId // Record the settlement transaction
                    ]);
                }
                
                DB::commit();
                return redirect()->route('institute.admissions.show', $id)->with('success', 'Cash payment settled successfully!');
            }

            // Finalize Admission (for normal Full/EMI payments)
            $params = [
                'payment_type' => $order->payment_category,
                'amount_paid' => $order->amount,
                'discount_value' => $order->notes['discount_value'] ?? 0,
                'discount_type' => $order->notes['discount_type'] ?? 'fixed',
                'emi_months' => $order->notes['emi_months'] ?? null,
                'payer_name' => $application->application_student->full_name,
                'payer_phone' => $application->application_student->phone_number,
                'transaction_id' => $razorpayPaymentId
            ];

            $result = $this->finalizeAdmissionPayment($application, $params, $order->notes['final_total'], 'captured', $order->id);

            DB::commit();

            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Callback processing failed: " . $e->getMessage());
            return redirect()->route('institute.admissions.show', $id)->with('error', 'Error finalizing payment: ' . $e->getMessage());
        }
    }

    public function paymentFailure(Request $request, $id)
    {
        $request->validate([
            'payment_id' => 'nullable',
            'order_id' => 'required',
            'error_code' => 'nullable',
            'error_description' => 'nullable',
        ]);

        $order = \App\Models\AdmissionPaymentOrder::where('razorpay_order_id', $request->order_id)->first();

        // Update Order Status to failed
        if ($order) {
            $order->update(['status' => 'failed']);
        }

        // Record Failed Transaction
        \App\Models\AdmissionPaymentTransaction::updateOrCreate(
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

    private function finalizeAdmissionPayment($application, $data, $finalAmount, $status, $orderId = null)
    {
        $user = auth()->user();
        $courseFee = $application->application_form->course_fee ?? 30000;

        $payment = AdmissionPayment::create([
            'student_application_id' => $application->id,
            'payment_type' => $data['payment_type'],
            'total_amount' => $courseFee,
            'discount_value' => $data['discount_value'] ?? 0,
            'discount_type' => $data['discount_type'] ?? 'fixed',
            'final_amount' => $finalAmount,
            'paid_amount' => $data['amount_paid'],
            'emi_months' => $data['emi_months'] ?? null,
            'payer_name' => $data['payer_name'] ?? null,
            'payer_phone' => $data['payer_phone'] ?? null,
            'receiver_name' => $user->name,
            'received_by' => $user->id,
            'status' => 'completed',
            'transaction_id' => $data['transaction_id'] ?? null,
            'remarks' => $data['payment_type'] == 'emi' ? 'EMI Down Payment' : 'Admission Fee Payment',
        ]);

        if ($orderId) {
            AdmissionPaymentOrder::where('id', $orderId)->update(['admission_payment_id' => $payment->id]);
        }

        // --- Admission Tracking Logic ---
        $admission = AdmissionStudent::where('student_application_id', $application->id)->first();

        if (!$admission) {
            $admission = new AdmissionStudent();
            $admission->admission_code = $this->generateAdmissionCode($application->application_form->institution);
            $admission->student_application_id = $application->id;
            $admission->application_code = $application->application_code;
            $admission->application_form_id = $application->application_form_id;
            $admission->student_id = $application->student_id;
            $admission->institution_id = $application->application_form->institution_id;
            $admission->academic_session = $application->application_form->session;
            $admission->payment_type = $data['payment_type'];
            $admission->total_payable_amount = $finalAmount;
            $admission->total_paid_amount = 0;

            if ($data['payment_type'] == 'emi') {
                $admission->emi_months = $data['emi_months'] ?? null;
                if ($admission->emi_months > 1) {
                    $balance = $finalAmount - $data['amount_paid'];
                    $admission->emi_amount = $balance / ($admission->emi_months - 1);
                }
            }
        }

        $admission->total_paid_amount += $data['amount_paid'];
        $admission->pending_amount = max(0, $admission->total_payable_amount - $admission->total_paid_amount);
        
        if ($admission->pending_amount <= 0) {
            $admission->status = 'completed';
            $admission->next_payment_date = null;
        } else {
            $admission->status = 'active';
            if ($admission->payment_type == 'emi') {
                $admission->next_payment_date = now()->addMonth();
            }
        }

        $admission->save();

        return redirect()->route('institute.admissions.show', $application->id)
            ->with('success', 'Admission payment recorded successfully! Admission Code: ' . $admission->admission_code);
    }

    private function generateAdmissionCode($institution)
    {
        $prefix = 'ADM';
        $instCode = $institution->institution_code ?? 'INST';
        $year = date('Y');
        
        $lastAdmission = AdmissionStudent::where('institution_id', $institution->id)
            ->whereYear('created_at', $year)
            ->latest()
            ->first();

        $sequence = 1;
        if ($lastAdmission) {
            $lastCode = $lastAdmission->admission_code;
            $parts = explode('/', $lastCode);
            $lastSeq = end($parts);
            if (is_numeric($lastSeq)) {
                $sequence = (int)$lastSeq + 1;
            }
        }

        return sprintf('%s/%s/%s/%05d', $prefix, $instCode, $year, $sequence);
    }

    public function settlePayment(Request $request, $id, $paymentId)
    {
        $application = StudentApplication::with('application_form.institution', 'application_student')->findOrFail($id);
        $paymentRecord = AdmissionPayment::findOrFail($paymentId);
        $institution = $application->application_form->institution;

        // Security check
        $user = auth()->user();
        if (!$institution || !$user->isInstitutionAdmin($institution->id)) {
             return response()->json(['success' => false, 'error' => 'Access Denied: You must be an administrator of this institution to settle payments.'], 403);
        }

        if ($paymentRecord->payment_type !== 'cash' || $paymentRecord->is_settled) {
            return response()->json(['success' => false, 'error' => 'Invalid settlement request.'], 400);
        }

        try {
            DB::beginTransaction();

            $receipt = "SETTLE_" . time();
            $notes = [
               'application_code' => $application->application_code,
               'student_name' => $application->application_student->full_name,
               'payment_type' => 'settlement',
               'original_payment_id' => $paymentRecord->id
            ];

            $orderResponse = $this->razorpayService->createOrder($institution, $paymentRecord->paid_amount, $receipt, $notes);

            if (!$orderResponse['success']) {
                throw new \Exception($orderResponse['error']);
            }

            // Create Order Record
            AdmissionPaymentOrder::create([
                'student_application_id' => $application->id,
                'application_form_id' => $application->application_form_id,
                'student_id' => $application->student_id,
                'institution_id' => $institution->id,
                'razorpay_order_id' => $orderResponse['order_id'],
                'amount' => $paymentRecord->paid_amount,
                'currency' => 'INR',
                'status' => 'pending',
                'payment_category' => 'settlement',
                'admission_payment_id' => $paymentRecord->id, // Link to original cash payment
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
                'callback_url' => route('institute.admissions.payment.callback', $application->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function downloadAcknowledgment($id)
    {
        $application = StudentApplication::with([
            'application_form.institution.organization',
            'application_student',
            'application_address',
            'application_parent',
            'application_academic_qualifications',
            'admission_student',
            'admission_payments.order.latest_transaction'
        ])->findOrFail($id);

        $institution = $application->application_form->institution;
        
        // Security check
        $user = auth()->user();
        if (!$institution || !$user->institutions()->where('institutions.id', $institution->id)->exists()) {
             abort(403, 'Access Denied');
        }

        if (!$application->admission_student) {
            return redirect()->back()->with('error', 'Admission record not found for this student.');
        }

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

    public function enrolled(Request $request)
    {
        $user = auth()->user();
        $institutions = $user->institutions()->latest()->get();
        
        $institutionId = $request->query('institution_id') ?? session('last_institution_id');
        $selectedInstitution = null;
        if ($institutionId && $institutions->isNotEmpty()) {
             $selectedInstitution = $institutions->where('id', $institutionId)->first();
        }
        
        if (!$selectedInstitution && $institutions->isNotEmpty()) {
            $selectedInstitution = $institutions->first();
        }

        $applicationForms = collect([]);
        $selectedFormId = $request->query('application_form_id');

        if ($selectedInstitution) {
            session(['last_institution_id' => $selectedInstitution->id]);
            $applicationForms = ApplicationForm::where('institution_id', $selectedInstitution->id)
                ->where('status', 'active')
                ->latest()
                ->get();
        }

        $query = AdmissionStudent::with(['student_application.application_student', 'application_form', 'institution']);

        if ($selectedInstitution) {
            $query->where('institution_id', $selectedInstitution->id);
        } else {
            $query->whereIn('institution_id', $institutions->pluck('id'));
        }

        if ($selectedFormId) {
            $query->where('application_form_id', $selectedFormId);
        }

        if ($request->filled('session')) {
            $query->where('academic_session', $request->session);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('admission_code', 'like', "%{$search}%")
                  ->orWhere('application_code', 'like', "%{$search}%")
                  ->orWhereHas('student_application.application_student', function($qs) use ($search) {
                      $qs->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $enrolledStudents = $query->latest()->paginate(15)->withQueryString();
        
        $sessions = AdmissionStudent::select('academic_session')
            ->distinct()
            ->whereIn('institution_id', $institutions->pluck('id'))
            ->pluck('academic_session');

        return view('institute.admissions.enrolled', compact(
            'institutions', 
            'selectedInstitution', 
            'applicationForms', 
            'selectedFormId', 
            'enrolledStudents',
            'sessions'
        ));
    }
}
