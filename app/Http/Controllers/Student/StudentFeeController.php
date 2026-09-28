<?php

namespace App\Http\Controllers\Student;

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

class StudentFeeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.onboarding')->with('error', 'Please complete onboarding first.');
        }

        // Get the active school enrollment
        $enrollment = SchoolEnrollment::with(['schoolClass', 'section', 'academicYear'])
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return view('student.fees.index', [
                'enrollment' => null,
                'months' => [],
                'config' => null,
                'history' => collect()
            ]);
        }

        $activeYear = $enrollment->academicYear;
        
        // Get expected amount & online payment config from config
        $config = SchoolFeeConfig::where('academic_year_id', $enrollment->academic_year_id)
            ->where('class_id', $enrollment->class_id)
            ->first();

        // Get history of payments
        $history = SchoolFeePayment::where('enrollment_id', $enrollment->id)
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $paymentsByMonth = $history->keyBy(function ($item) {
            return $item->month . '-' . $item->year;
        });

        // Determine all academic year months
        $months = [];
        if ($activeYear && $activeYear->start_date && $activeYear->end_date) {
            $start = Carbon::parse($activeYear->start_date)->startOfMonth();
            $end = Carbon::parse($activeYear->end_date)->startOfMonth();
            
            $current = $start->copy();
            while ($current->lte($end)) {
                $key = $current->month . '-' . $current->year;
                $payment = $paymentsByMonth->get($key);
                
                $months[] = [
                    'month_number' => $current->month,
                    'year' => $current->year,
                    'name' => $current->format('F'),
                    'formatted' => $current->format('F Y'),
                    'is_paid' => $payment && $payment->status === 'paid',
                    'payment' => $payment
                ];
                $current->addMonth();
            }
        }

        return view('student.fees.index', compact('enrollment', 'config', 'months', 'history'));
    }

    /**
     * Create Razorpay order for monthly academic fee
     */
    public function createRazorpayOrder(Request $request, RazorpayService $razorpayService)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:school_enrollments,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();
        $student = $user->student;
        $enrollment = SchoolEnrollment::where('student_id', $student->id)
            ->where('id', $request->enrollment_id)
            ->firstOrFail();

        // Ensure online payments are allowed for this class
        $config = SchoolFeeConfig::where('academic_year_id', $enrollment->academic_year_id)
            ->where('class_id', $enrollment->class_id)
            ->first();

        if (!$config || !$config->allow_student_payment) {
            return response()->json(['success' => false, 'error' => 'Online payments are currently disabled for your class.'], 400);
        }

        // Get organization and institution
        $org = $enrollment->schoolClass->organization;
        $institution = $org->institutions()->first();

        if (!$institution || !$institution->razorpay_key) {
            return response()->json(['success' => false, 'error' => 'Payment gateway is not configured for this institution.'], 400);
        }

        $receipt = 'STUDENT-FEE-' . $enrollment->id . '-' . $request->month . '-' . $request->year . '-' . time();
        $notes = [
            'student_full_name' => $student->full_name,
            'enrollment_id' => $enrollment->id,
            'month' => $request->month,
            'year' => $request->year,
            'type' => 'monthly_fee'
        ];

        $order = $razorpayService->createOrder($institution, $request->amount, $receipt, $notes);

        if ($order['success']) {
            return response()->json([
                'success' => true,
                'order_id' => $order['order_id'],
                'amount' => $order['amount'],
                'key' => $institution->razorpay_key,
                'student_name' => $student->full_name,
                'student_email' => $student->email,
                'student_phone' => $student->phone_number,
            ]);
        }

        return response()->json(['success' => false, 'error' => $order['error']], 500);
    }

    /**
     * Verify online payment and record
     */
    public function verifyRazorpayPayment(Request $request, RazorpayService $razorpayService)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:school_enrollments,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'amount_paid' => 'required|numeric|min:1',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $user = auth()->user();
        $student = $user->student;
        $enrollment = SchoolEnrollment::where('student_id', $student->id)
            ->where('id', $request->enrollment_id)
            ->firstOrFail();

        // Get organization and institution
        $org = $enrollment->schoolClass->organization;
        $institution = $org->institutions()->first();

        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];

        if ($razorpayService->verifySignature($institution, $attributes)) {
            // Log payment into school_fee_payments table
            SchoolFeePayment::updateOrCreate(
                [
                    'student_id' => $enrollment->student_id,
                    'academic_year_id' => $enrollment->academic_year_id,
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
                    'remarks' => 'Paid online by student via Dashboard',
                    'payment_payload' => $request->all(),
                ]
            );

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'error' => 'Invalid transaction signature.'], 400);
    }
}
