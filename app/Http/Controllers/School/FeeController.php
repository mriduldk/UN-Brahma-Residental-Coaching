<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School\SchoolClass;
use App\Models\School\SchoolEnrollment;
use App\Models\School\AcademicYear;
use App\Models\School\SchoolFeeConfig;
use App\Models\School\SchoolFeePayment;
use App\Models\Institution;
use App\Services\RazorpayService;
use Carbon\Carbon;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        $activeYear = AcademicYear::where('organization_id', $orgId)
            ->where('is_active', true)
            ->first();

        $classId = $request->class_id;
        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');

        $classes = SchoolClass::where('organization_id', $orgId)->get();
        
        $enrollments = [];
        if ($classId && $activeYear) {
            $enrollments = SchoolEnrollment::with(['student', 'schoolClass', 'section'])
                ->where('academic_year_id', $activeYear->id)
                ->where('class_id', $classId)
                ->where('status', 'active')
                ->get();

            // Fetch payments for these students for selected month/year
            $payments = SchoolFeePayment::where('academic_year_id', $activeYear->id)
                ->where('month', $month)
                ->where('year', $year)
                ->get()
                ->keyBy('student_id');

            foreach ($enrollments as $enrollment) {
                $enrollment->payment = $payments->get($enrollment->student_id);
            }
        }

        return view('school.fees.index', compact('classes', 'activeYear', 'enrollments', 'month', 'year', 'classId'));
    }

    public function config()
    {
        $orgId = auth()->user()->currentOrganization()->id;
        $activeYear = AcademicYear::where('organization_id', $orgId)
            ->where('is_active', true)
            ->first();

        $classes = SchoolClass::where('organization_id', $orgId)->get();
        $configs = SchoolFeeConfig::where('academic_year_id', $activeYear->id)
            ->get()
            ->keyBy('class_id');

        return view('school.fees.config', compact('classes', 'activeYear', 'configs'));
    }

    public function storeConfig(Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        $activeYearId = $request->academic_year_id;

        foreach ($request->fees as $classId => $amount) {
            if ($amount === null || $amount === '') continue;

            $allowPayment = isset($request->allow_student_payment[$classId]);

            SchoolFeeConfig::updateOrCreate(
                [
                    'organization_id' => $orgId,
                    'academic_year_id' => $activeYearId,
                    'class_id' => $classId
                ],
                [
                    'monthly_amount' => $amount,
                    'allow_student_payment' => $allowPayment
                ]
            );
        }

        return back()->with('success', 'Fee configuration updated successfully.');
    }

    public function collect($enrollmentId, Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        $enrollment = SchoolEnrollment::with(['student', 'schoolClass', 'section'])
            ->findOrFail($enrollmentId);

        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');

        // Get expected amount from config
        $config = SchoolFeeConfig::where('academic_year_id', $enrollment->academic_year_id)
            ->where('class_id', $enrollment->class_id)
            ->first();

        $existingPayment = SchoolFeePayment::where('enrollment_id', $enrollmentId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        $isPaidOnline = $existingPayment && $existingPayment->status === 'paid' && $existingPayment->payment_method === 'online';

        return view('school.fees.collect', compact('enrollment', 'config', 'month', 'year', 'existingPayment', 'isPaidOnline'));
    }

    public function storePayment(Request $request)
    {
        $orgId = auth()->user()->currentOrganization()->id;
        
        $request->validate([
            'enrollment_id' => 'required|exists:school_enrollments,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        $enrollment = SchoolEnrollment::findOrFail($request->enrollment_id);

        SchoolFeePayment::updateOrCreate(
            [
                'organization_id' => $orgId,
                'academic_year_id' => $enrollment->academic_year_id,
                'student_id' => $enrollment->student_id,
                'enrollment_id' => $enrollment->id,
                'month' => $request->month,
                'year' => $request->year,
            ],
            [
                'amount_paid' => $request->amount_paid,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'status' => 'paid', // For now assuming full payment
                'remarks' => $request->remarks,
            ]
        );

        return redirect()->route('school.fees.index', ['class_id' => $enrollment->class_id, 'month' => $request->month, 'year' => $request->year])
            ->with('success', 'Fee payment recorded successfully.');
    }

    /**
     * Create a Razorpay Order for monthly fee
     */
    public function createRazorpayOrder(Request $request, RazorpayService $razorpayService)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:school_enrollments,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $org = auth()->user()->currentOrganization();
        $institution = $org->institutions()->first();

        if (!$institution || !$institution->razorpay_key) {
            return response()->json(['success' => false, 'error' => 'Razorpay not configured for this institution.'], 400);
        }

        $enrollment = SchoolEnrollment::with('student')->findOrFail($request->enrollment_id);
        
        $receipt = 'FEE-' . $enrollment->id . '-' . time();
        $notes = [
            'student_full_name' => $enrollment->student->full_name,
            'enrollment_id' => $enrollment->id,
            'type' => 'monthly_fee'
        ];

        $order = $razorpayService->createOrder($institution, $request->amount, $receipt, $notes);

        if ($order['success']) {
            return response()->json([
                'success' => true,
                'order_id' => $order['order_id'],
                'amount' => $order['amount'],
                'key' => $institution->razorpay_key,
                'student_name' => $enrollment->student->full_name,
                'student_email' => $enrollment->student->email,
                'student_phone' => $enrollment->student->phone_number,
            ]);
        }

        return response()->json(['success' => false, 'error' => $order['error']], 500);
    }

    /**
     * Verify Razorpay Payment and save record
     */
    public function verifyRazorpayPayment(Request $request, RazorpayService $razorpayService)
    {
        $org = auth()->user()->currentOrganization();
        $institution = $org->institutions()->first();

        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];

        if ($razorpayService->verifySignature($institution, $attributes)) {
            // Payment is valid, save to database
            $enrollment = SchoolEnrollment::findOrFail($request->enrollment_id);
            
            $activeYear = AcademicYear::where('organization_id', $org->id)->where('is_active', true)->first();

            SchoolFeePayment::updateOrCreate(
                [
                    'student_id' => $enrollment->student_id,
                    'academic_year_id' => $activeYear->id,
                    'month' => $request->month,
                    'year' => $request->year,
                ],
                [
                    'organization_id' => $org->id,
                    'enrollment_id' => $enrollment->id,
                    'amount_paid' => $request->amount_paid,
                    'payment_date' => date('Y-m-d'),
                    'payment_method' => 'online',
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'status' => 'paid',
                    'remarks' => ($request->remarks ?? '') . ' (Paid via Razorpay)',
                    'payment_payload' => $request->all(),
                ]
            );

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'error' => 'Invalid signature'], 400);
    }
}
