<?php

namespace App\Http\Controllers;

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
        $org = $user->currentOrganization();
        
        if (!$org) {
             abort(403, 'No organization associated.');
        }

        $institutionId = $request->query('institution_id') ?? session('last_admin_institution_id');
        $institutions = $org->institutions()->latest()->get();
        
        $selectedInstitution = null;
        if ($institutionId && $institutions->isNotEmpty()) {
             $selectedInstitution = $institutions->where('id', $institutionId)->first();
        }
        
        if (!$selectedInstitution && $institutions->isNotEmpty()) {
            $selectedInstitution = $institutions->first();
        }

        $applicationForms = collect([]);
        $selectedFormId = $request->query('application_form_id') ?? session('last_admin_application_form_id');

        if ($selectedInstitution) {
            session(['last_admin_institution_id' => $selectedInstitution->id]);

            $applicationForms = ApplicationForm::where('institution_id', $selectedInstitution->id)
                ->where('status', 'active')
                ->latest()
                ->get();
            
            if ($selectedFormId && !$applicationForms->find($selectedFormId)) {
                $selectedFormId = null;
            }
            session(['last_admin_application_form_id' => $selectedFormId]);
        }

        $admissions = collect([]);
        $selectedAdmission = null;

        if ($selectedInstitution) {
            $admissionsQuery = \App\Models\Admission::where('institution_id', $selectedInstitution->id)
                ->where('status', 'active');
            
            if ($selectedFormId) {
                $admissionsQuery->where('application_form_id', $selectedFormId);
            }

            $admissions = $admissionsQuery->latest()->get();
            $admissionId = $request->query('admission_id') ?? session('last_admin_admission_id');

            if ($admissionId) {
                $selectedAdmission = $admissions->where('id', $admissionId)->first();
            }

            if (!$selectedAdmission && $admissions->isNotEmpty()) {
                $selectedAdmission = $admissions->where('status', 'active')->first() ?? $admissions->first();
            }

            if ($selectedAdmission) {
                session(['last_admin_admission_id' => $selectedAdmission->id]);
            }
        }

        $stats = [
            'total_admissions' => 0,
            'total_revenue' => 0,
            'unsettled_cash' => 0,
            'full_payment' => 0,
            'emi_payment' => 0,
        ];

        if ($selectedInstitution) {
            $applicationsQuery = StudentApplication::with(['application_form', 'application_student', 'admission_student', 'admission_payments'])
                ->where('status', 'submitted')
                ->whereHas('application_form', function($q) use ($selectedInstitution) {
                    $q->where('institution_id', $selectedInstitution->id);
                });

            if ($selectedAdmission) {
                if ($selectedAdmission->application_form_id) {
                     $applicationsQuery->where('application_form_id', $selectedAdmission->application_form_id);
                } else {
                    $applicationsQuery->whereHas('application_form', function($q) use ($selectedAdmission) {
                        $q->where('session', $selectedAdmission->academic_session);
                    });
                }
            }

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

            $statsData = AdmissionStudent::where('institution_id', $selectedInstitution->id);
            if ($selectedFormId) { $statsData->where('application_form_id', $selectedFormId); }
            if ($selectedAdmission) { $statsData->where('academic_session', $selectedAdmission->academic_session); }
            
            $admissionsList = $statsData->with(['admission_payments'])->get();
            $stats['total_admissions'] = $admissionsList->count();
            
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
                        if ($dueDate->lessThanOrEqualTo($nextWeek)) { $stats['upcoming_dues_count']++; }
                    }
                } else {
                    $stats['full_payment']++;
                }
                $stats['unsettled_cash'] += $adm->admission_payments->where('payment_type', 'cash')->where('is_settled', false)->sum('paid_amount');
            }
        } else {
            $applications = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
        }
        
        return view('administration.admissions.index', compact('institutions', 'selectedInstitution', 'applicationForms', 'selectedFormId', 'admissions', 'selectedAdmission', 'applications', 'stats'));
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

        $user = auth()->user();
        $org = $user->currentOrganization();
        if (!$org || $application->application_form->organization_id !== $org->id) {
             abort(403, 'Access Denied');
        }

        $admissionConfig = \App\Models\Admission::where('application_form_id', $application->application_form_id)
            ->where('status', 'active')
            ->first();
        
        return view('administration.admissions.show', compact('application', 'admissionConfig'));
    }

    public function storePayment(Request $request, $id)
    {
        $application = StudentApplication::with('application_form', 'application_student')->findOrFail($id);
        $institution = $application->application_form->institution;
        $org = auth()->user()->currentOrganization();
        
        if (!$org || $institution->organization_id !== $org->id) {
             abort(403, 'Access Denied');
        }

        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|in:full,emi,cash,dd_full,dd_emi',
            'amount_paid' => 'required|numeric|min:1',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'emi_months' => 'nullable|integer|min:1',
            'payer_name' => 'nullable|string|required_if:payment_type,cash',
            'payer_phone' => 'nullable|string|required_if:payment_type,cash',
            'dd_number' => 'nullable|string|required_if:payment_type,dd_full,dd_emi',
            'dd_date' => 'nullable|date|required_if:payment_type,dd_full,dd_emi',
            'dd_bank' => 'nullable|string|required_if:payment_type,dd_full,dd_emi',
            'dd_attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120|required_if:payment_type,dd_full,dd_emi',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) { return response()->json(['success' => false, 'error' => implode(', ', $validator->errors()->all())], 422); }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $admission = AdmissionStudent::where('student_application_id', $application->id)->first();
        if ($admission) {
            $finalAmount = $admission->total_payable_amount;
        } else {
            $courseFee = $application->application_form->course_fee ?? 30000;
            $discountAmount = 0;
            if ($request->discount_value > 0) {
                if ($request->discount_type == 'percentage') { $discountAmount = ($courseFee * $request->discount_value) / 100; }
                else { $discountAmount = $request->discount_value; }
            }
            $finalAmount = $courseFee - $discountAmount;
        }

        if (in_array($request->payment_type, ['emi', 'dd_emi']) && !$request->emi_months && (!$admission || !$admission->emi_months)) {
             if ($request->ajax()) { return response()->json(['success' => false, 'error' => 'The emi months field is required.'], 422); }
            return redirect()->back()->with('error', 'EMI tenure is required.');
        }

        if (in_array($request->payment_type, ['cash', 'dd_full', 'dd_emi'])) {
            $paymentData = $request->all();
            
            if ($request->hasFile('dd_attachment')) {
                $file = $request->file('dd_attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/demand_drafts', $filename);
                // Save relative path for easy URL generation later
                $paymentData['dd_attachment_path'] = str_replace('public/', 'storage/', $path);
            }

            // If it's a subsequent EMI payment, we don't recalculate final amount off the course fee here
            // It will just record the amount paid against the pending balance
            return $this->finalizeAdmissionPayment($application, $paymentData, $finalAmount, 'completed');
        }

        try {
            DB::beginTransaction();
            $receipt = "ADM_ORD_" . time();
            $notes = [
               'application_code' => $application->application_code,
               'student_name' => $application->application_student->full_name,
               'payment_type' => $request->payment_type
            ];

            $orderResponse = $this->razorpayService->createOrder($institution, $request->amount_paid, $receipt, $notes);
            if (!$orderResponse['success']) { throw new \Exception($orderResponse['error']); }

            AdmissionPaymentOrder::create([
                'student_application_id' => $application->id,
                'application_form_id' => $application->application_form_id,
                'student_id' => $application->student_id,
                'institution_id' => $institution->id,
                'razorpay_order_id' => $orderResponse['order_id'],
                'amount' => $request->amount_paid,
                'currency' => 'INR',
                'status' => 'pending',
                'payment_category' => $request->payment_type,
                'user_id' => auth()->id(),
                'receipt' => $receipt,
                'notes' => [
                    'discount_value' => $request->discount_value,
                    'discount_type' => $request->discount_type,
                    'emi_months' => $request->emi_months,
                    'final_total' => $finalAmount
                ]
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $orderResponse['order_id'],
                    'amount' => $orderResponse['amount'],
                    'key' => $institution->razorpay_key,
                    'name' => $institution->institution_name,
                    'email' => $application->application_student->email ?? '',
                    'contact' => $application->application_student->phone_number ?? '',
                    'callback_url' => route('administration.admissions.payment.callback', $application->id)
                ]);
            }
            return response()->json(['error' => 'AJAX request expected'], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) { return response()->json(['success' => false, 'error' => $e->getMessage()], 422); }
            return redirect()->back()->with('error', 'Could not initiate payment: ' . $e->getMessage());
        }
    }

    public function handlePaymentCallback(Request $request, $id)
    {
        $application = StudentApplication::with('application_form.institution')->findOrFail($id);
        $institution = $application->application_form->institution;

        $isValid = $this->razorpayService->verifySignature($institution, [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ]);

        if (!$isValid) {
            $order = AdmissionPaymentOrder::where('razorpay_order_id', $request->razorpay_order_id)->first();
            AdmissionPaymentTransaction::create([
                'admission_payment_order_id' => $order->id ?? null,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'amount' => 0, 'currency' => 'INR', 'status' => 'verification_failed', 'json_response' => $request->all()
            ]);
            if ($order) { $order->update(['status' => 'failed']); }
            return redirect()->route('administration.admissions.show', $id)->with('error', 'Payment verification failed.');
        }

        try {
            DB::beginTransaction();
            $order = AdmissionPaymentOrder::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();
            $api = $this->razorpayService->getApi($institution);
            $payment = $api->payment->fetch($request->razorpay_payment_id);

            AdmissionPaymentTransaction::create([
                'admission_payment_order_id' => $order->id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'amount' => $payment->amount / 100, 'currency' => $payment->currency, 'status' => $payment->status,
                'method' => $payment->method, 'bank' => $payment->bank, 'wallet' => $payment->wallet,
                'vpa' => $payment->vpa, 'email' => $payment->email, 'contact' => $payment->contact,
                'fee' => $payment->fee / 100, 'tax' => $payment->tax / 100, 'json_response' => $payment->toArray()
            ]);

            $order->update(['status' => 'paid']);

            if ($order->payment_category == 'settlement') {
                $paymentRecord = AdmissionPayment::find($order->admission_payment_id);
                if ($paymentRecord) { $paymentRecord->update(['is_settled' => true, 'settled_at' => now(), 'transaction_id' => $request->razorpay_payment_id]); }
                DB::commit();
                return redirect()->route('administration.admissions.show', $id)->with('success', 'Cash payment settled successfully!');
            }

            $params = [
                'payment_type' => $order->payment_category,
                'amount_paid' => $order->amount,
                'discount_value' => $order->notes['discount_value'] ?? 0,
                'discount_type' => $order->notes['discount_type'] ?? 'fixed',
                'emi_months' => $order->notes['emi_months'] ?? null,
                'payer_name' => $application->application_student->full_name,
                'payer_phone' => $application->application_student->phone_number,
                'transaction_id' => $request->razorpay_payment_id
            ];

            $result = $this->finalizeAdmissionPayment($application, $params, $order->notes['final_total'], 'captured', $order->id);
            DB::commit();
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Callback processing failed: " . $e->getMessage());
            return redirect()->route('administration.admissions.show', $id)->with('error', 'Error finalizing payment.');
        }
    }

    public function paymentFailure(Request $request, $id)
    {
        $order = \App\Models\AdmissionPaymentOrder::where('razorpay_order_id', $request->order_id)->first();
        if ($order) { $order->update(['status' => 'failed']); }
        \App\Models\AdmissionPaymentTransaction::updateOrCreate(
            ['razorpay_payment_id' => $request->payment_id ?? 'failed_' . uniqid()],
            [
                'admission_payment_order_id' => $order->id ?? null,
                'amount' => 0, 'currency' => 'INR', 'status' => 'failed',
                'error_code' => $request->error_code, 'error_description' => $request->error_description, 'json_response' => $request->all(),
            ]
        );
        return response()->json(['status' => 'logged']);
    }

    private function finalizeAdmissionPayment($application, $data, $finalAmount, $status, $orderId = null)
    {
        $user = auth()->user();
        $courseFee = $application->application_form->course_fee ?? 30000;

        $remarks = 'Admission Fee Payment';
        if (in_array($data['payment_type'], ['emi', 'dd_emi'])) {
            $admission = AdmissionStudent::where('student_application_id', $application->id)->first();
            if ($admission) {
                $remarks = 'EMI Installment Payment';
            } else {
                $remarks = 'EMI Down Payment';
            }
        }
        if (in_array($data['payment_type'], ['dd_full', 'dd_emi'])) {
            $remarks .= ' via Demand Draft';
        }

        $payment = AdmissionPayment::create([
            'student_application_id' => $application->id,
            'payment_type' => $data['payment_type'], 'total_amount' => $courseFee,
            'discount_value' => $data['discount_value'] ?? 0, 'discount_type' => $data['discount_type'] ?? 'fixed',
            'final_amount' => $finalAmount, 'paid_amount' => $data['amount_paid'], 'emi_months' => $data['emi_months'] ?? null,
            'payer_name' => $data['payer_name'] ?? null, 'payer_phone' => $data['payer_phone'] ?? null,
            'receiver_name' => $user->name, 'received_by' => $user->id, 'status' => 'completed',
            'transaction_id' => $data['transaction_id'] ?? null,
            'remarks' => $remarks,
            'dd_number' => $data['dd_number'] ?? null,
            'dd_date' => $data['dd_date'] ?? null,
            'dd_bank' => $data['dd_bank'] ?? null,
            'dd_attachment_path' => $data['dd_attachment_path'] ?? null,
        ]);

        if ($orderId) { AdmissionPaymentOrder::where('id', $orderId)->update(['admission_payment_id' => $payment->id]); }

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

            if (in_array($data['payment_type'], ['emi', 'dd_emi'])) {
                $admission->emi_months = $data['emi_months'] ?? null;
                if ($admission->emi_months > 1) {
                    $balance = $finalAmount - $data['amount_paid'];
                    $admission->emi_amount = $balance / ($admission->emi_months - 1);
                }
            }
        }

        $admission->total_paid_amount += $data['amount_paid'];
        $admission->pending_amount = max(0, $admission->total_payable_amount - $admission->total_paid_amount);
        $admission->status = $admission->pending_amount <= 0 ? 'completed' : 'active';
        if ($admission->status == 'active' && in_array($admission->payment_type, ['emi', 'dd_emi'])) { $admission->next_payment_date = now()->addMonth(); }
        else { $admission->next_payment_date = null; }
        $admission->save();

        return redirect()->route('administration.admissions.show', $application->id)->with('success', 'Admission success! Code: ' . $admission->admission_code);
    }

    private function generateAdmissionCode($institution)
    {
        $prefix = 'ADM';
        $instCode = $institution->institution_code ?? 'INST';
        $year = date('Y');
        $lastAdmission = AdmissionStudent::where('institution_id', $institution->id)->whereYear('created_at', $year)->latest()->first();
        $sequence = 1;
        if ($lastAdmission) {
            $parts = explode('/', $lastAdmission->admission_code);
            $lastSeq = end($parts);
            if (is_numeric($lastSeq)) { $sequence = (int)$lastSeq + 1; }
        }
        return sprintf('%s/%s/%s/%05d', $prefix, $instCode, $year, $sequence);
    }

    public function settlePayment(Request $request, $id, $paymentId)
    {
        $application = StudentApplication::with('application_form.institution', 'application_student')->findOrFail($id);
        $paymentRecord = AdmissionPayment::findOrFail($paymentId);
        $institution = $application->application_form->institution;
        $org = auth()->user()->currentOrganization();
        
        if (!$org || $institution->organization_id !== $org->id) { abort(403); }

        try {
            DB::beginTransaction();
            $receipt = "SETTLE_" . time();
            $notes = ['application_code' => $application->application_code, 'student_name' => $application->application_student->full_name, 'payment_type' => 'settlement', 'original_payment_id' => $paymentRecord->id];
            $orderResponse = $this->razorpayService->createOrder($institution, $paymentRecord->paid_amount, $receipt, $notes);
            if (!$orderResponse['success']) { throw new \Exception($orderResponse['error']); }

            AdmissionPaymentOrder::create([
                'student_application_id' => $application->id, 'application_form_id' => $application->application_form_id, 'student_id' => $application->student_id, 'institution_id' => $institution->id,
                'razorpay_order_id' => $orderResponse['order_id'], 'amount' => $paymentRecord->paid_amount, 'currency' => 'INR', 'status' => 'pending', 'payment_category' => 'settlement',
                'admission_payment_id' => $paymentRecord->id, 'user_id' => auth()->id(), 'receipt' => $receipt, 'notes' => $notes
            ]);

            DB::commit();
            return response()->json([
                'success' => true, 'order_id' => $orderResponse['order_id'], 'amount' => $orderResponse['amount'], 'key' => $institution->razorpay_key, 'name' => $institution->institution_name,
                'email' => $application->application_student->email ?? '', 'contact' => $application->application_student->phone_number ?? '', 'callback_url' => route('administration.admissions.payment.callback', $application->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function downloadAcknowledgment($id)
    {
        $application = StudentApplication::with(['application_form.institution.organization', 'application_student', 'application_address', 'application_parent', 'application_academic_qualifications', 'admission_student', 'admission_payments.order.latest_transaction'])->findOrFail($id);
        $org = auth()->user()->currentOrganization();
        if (!$org || $application->application_form->organization_id !== $org->id) { abort(403); }

        $data = [
            'application' => $application, 'student' => $application->application_student, 'admission' => $application->admission_student,
            'institution' => $application->application_form->institution, 'organization' => $org, 'payments' => $application->admission_payments()->orderBy('created_at', 'desc')->get(),
        ];
        return Pdf::loadView('administration.admissions.acknowledgment', $data)->download('Acknowledgment_' . str_replace('/', '_', $application->admission_student->admission_code) . '.pdf');
    }
    public function enrolled(Request $request)
    {
        $user = auth()->user();
        $org = $user->currentOrganization();
        
        if (!$org) {
             abort(403, 'No organization associated.');
        }

        $institutions = $org->institutions()->latest()->get();
        $institutionId = $request->query('institution_id');
        $selectedInstitution = $institutionId ? $institutions->where('id', $institutionId)->first() : null;

        $applicationForms = collect([]);
        $selectedFormId = $request->query('application_form_id');

        if ($selectedInstitution) {
            $applicationForms = ApplicationForm::where('institution_id', $selectedInstitution->id)
                ->where('status', 'active')
                ->latest()
                ->get();
        }

        $query = AdmissionStudent::with(['student_application.application_student', 'application_form', 'institution'])
            ->whereHas('institution', function($q) use ($org) {
                $q->where('organization_id', $org->id);
            });

        if ($selectedInstitution) {
            $query->where('institution_id', $selectedInstitution->id);
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
            ->whereHas('institution', function($q) use ($org) {
                $q->where('organization_id', $org->id);
            })
            ->pluck('academic_session');

        return view('administration.admissions.enrolled', compact(
            'institutions', 
            'selectedInstitution', 
            'applicationForms', 
            'selectedFormId', 
            'enrolledStudents',
            'sessions'
        ));
    }
    public function exportEnrolled(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) abort(403);

        $query = AdmissionStudent::with([
            'student_application.application_student',
            'student_application.application_form.institution'
        ])->whereHas('institution', function($q) use ($org) {
            $q->where('organization_id', $org->id);
        });

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->filled('form_id')) {
            $query->where('application_form_id', $request->form_id);
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

        $enrolledStudents = $query->latest()->get();

        $csvFileName = 'enrolled_students_' . date('Y-m-d_H-i') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($enrolledStudents) {
            $file = fopen('php://output', 'w');
            
            // Header Row
            fputcsv($file, [
                'Admission No.', 
                'Student Name',
                'Phone',
                'Email',
                'Institution Name',
                'Trade / Form',
                'Academic Session',
                'Admitted On',
                'Payment Type',
                'Total Fee (Rs.)',
                'Paid (Rs.)',
                'Pending (Rs.)',
                'Status'
            ]);

            // Data Rows
            foreach ($enrolledStudents as $adm) {
                $app = $adm->student_application;
                $student = $app ? $app->application_student : null;
                $form = $app ? $app->application_form : null;
                $inst = $form ? $form->institution : null;

                fputcsv($file, [
                    $adm->admission_code,
                    $student->full_name ?? '-',
                    $student->phone_number ?? '-',
                    $student->email ?? '-',
                    $inst->institution_name ?? '-',
                    $form->application_name ?? '-',
                    $adm->academic_session,
                    $adm->created_at->format('d M, Y'),
                    str_replace('_', ' ', strtoupper($adm->payment_type)),
                    $adm->total_payable_amount,
                    $adm->total_paid_amount,
                    $adm->pending_amount,
                    strtoupper($adm->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
