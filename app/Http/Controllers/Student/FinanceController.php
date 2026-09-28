<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdmissionPaymentTransaction;
use App\Models\AdmissionStudent;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.onboarding');
        }

        $query = $request->query('q');
        $status_filter = $request->query('status');

        // Registration Transactions
        $regTransactions = PaymentTransaction::with(['application.application_form', 'order'])
            ->whereHas('application', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            });

        // Admission Transactions
        $admTransactions = AdmissionPaymentTransaction::with(['order.student_application.application_form', 'order'])
            ->whereHas('order.student_application', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            });

        // Apply Search
        if ($query) {
            $regTransactions->where(function ($q) use ($query) {
                $q->where('razorpay_payment_id', 'like', "%{$query}%")
                  ->orWhereHas('application.application_form', function($qf) use ($query) {
                      $qf->where('application_name', 'like', "%{$query}%");
                  });
            });

            $admTransactions->where(function ($q) use ($query) {
                $q->where('razorpay_payment_id', 'like', "%{$query}%")
                  ->orWhereHas('order.student_application.application_form', function($qf) use ($query) {
                      $qf->where('application_name', 'like', "%{$query}%");
                  });
            });
        }

        // Apply Status Filter
        if ($status_filter) {
            $status_map = [
                'captured' => 'paid',
                'failed' => 'failed',
                'created' => 'pending'
            ];
            
            $regTransactions->where('status', $status_filter);
            
            $admTransactions->whereHas('order', function ($q) use ($status_filter, $status_map) {
                $q->where('status', $status_map[$status_filter] ?? $status_filter);
            });
        }

        $regData = $regTransactions->get()->map(function ($t) {
            $t->payment_type = 'registration';
            return $t;
        });

        $admData = $admTransactions->get()->map(function ($t) {
            $t->payment_type = 'admission';
            return $t;
        });

        $transactions = $regData->concat($admData)->sortByDesc('created_at')->values();

        return view('student.finance.index', compact('transactions'));
    }

    public function show($type, $id)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403);
        }

        $admission_student = null;

        if ($type === 'registration') {
            $transaction = PaymentTransaction::with(['application.application_form', 'order'])
                ->whereHas('application', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->findOrFail($id);
            $order = $transaction->order;
            $purpose_title = "Registration for " . ($transaction->application->application_form->application_name ?? 'N/A');
            $application = $transaction->application;
        } elseif ($type === 'admission') {
            $transaction = AdmissionPaymentTransaction::with(['order.student_application.application_form', 'order'])
                ->whereHas('order.student_application', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->findOrFail($id);
            $order = $transaction->order;
            $purpose_title = "Admission for " . ($order->application_form->application_name ?? 'N/A');
            $application = $order->student_application;

            if ($application) {
                $admission_student = AdmissionStudent::where('student_application_id', $application->id)->first();
            }
        } else {
            abort(404);
        }

        return view('student.finance.show', compact('transaction', 'order', 'purpose_title', 'application', 'type', 'admission_student'));
    }

    public function refresh(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:registration,admission',
            'id' => 'required'
        ]);

        $type = $request->type;
        $id = $request->id;

        // Fetch transaction ensuring it belongs to the student
        if ($type === 'registration') {
            $transaction = PaymentTransaction::with('order')->whereHas('application', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })->findOrFail($id);
            $order = $transaction->order;
            $institution = $transaction->application->application_form->institution;
        } else {
            $transaction = AdmissionPaymentTransaction::with('order')->whereHas('order.student_application', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })->findOrFail($id);
            $order = $transaction->order;
            $institution = $order->application_form->institution;
        }

        if (!$order || !$order->razorpay_order_id) {
            return back()->with('error', 'Razorpay Order ID not found for this transaction.');
        }

        $key = $institution->razorpay_key;
        $secret = $institution->razorpay_secret;

        if (!$key || !$secret) {
             // Students shouldn't see configuration errors, but we need to handle it.
            return back()->with('error', 'Payment gateway configuration issue. Please contact the institution.');
        }

        try {
            $response = Http::withBasicAuth($key, $secret)
                ->get('https://api.razorpay.com/v1/orders/' . $order->razorpay_order_id . '/payments');

            if ($response->successful()) {
                $payments = $response->json();
                $updated = false;

                if (isset($payments['items']) && count($payments['items']) > 0) {
                    foreach ($payments['items'] as $payment) {
                        if ($payment['status'] === 'captured') {
                            if ($type === 'registration') {
                                $order->update(['status' => 'paid']);
                                if ($order->application) {
                                    $order->application->update(['payment_status' => 'paid']);
                                }
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
                                $order->update(['status' => 'paid']);
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
                }

                if ($updated) {
                    return back()->with('success', 'Payment status updated successfully!');
                } else {
                    return back()->with('warning', 'No successful payment found for this transaction.');
                }
            } else {
                return back()->with('error', 'Unable to fetch payment status at this time.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while refreshing status.');
        }
    }
}
