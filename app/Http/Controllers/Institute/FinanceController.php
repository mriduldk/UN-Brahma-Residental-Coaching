<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\AdmissionPaymentOrder;
use App\Models\AdmissionPaymentTransaction;
use App\Models\AdmissionStudent;
use App\Models\Institution;
use App\Models\PaymentOrder;
use App\Models\PaymentTransaction;
use App\Models\StudentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $institutions = $user->institutions;
        $institutionIds = $institutions->pluck('id')->toArray();

        // Stats Logic
        $totalCollected = 0;
        $todayCollected = 0;
        $pendingPayments = 0;

        // Statistics calculation (restricted to user's institutions)
        // Today Collected
        $todayCollectedReg = PaymentTransaction::whereHas('application.application_form', function ($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'captured')->whereDate('created_at', today())->sum('amount');

        $todayCollectedAdm = AdmissionPaymentTransaction::whereHas('order.application_form', function ($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'captured')->whereDate('created_at', today())->sum('amount');

        $todayCollected = $todayCollectedReg + $todayCollectedAdm;

        // Total Collected
        $totalCollectedReg = PaymentTransaction::whereHas('application.application_form', function ($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'captured')->sum('amount');

        $totalCollectedAdm = AdmissionPaymentTransaction::whereHas('order.application_form', function ($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'captured')->sum('amount');

        $totalCollected = $totalCollectedReg + $totalCollectedAdm;

        // Pending Payments (Wait for Razorpay sync)
        $pendingReg = PaymentOrder::whereHas('application.application_form', function ($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'created')->count();

        $pendingAdm = AdmissionPaymentOrder::whereHas('application_form', function ($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'pending')->count();
        $pendingPayments = $pendingReg + $pendingAdm;

        // Transactions Query
        $q = $request->query('q');
        $institution_id = $request->query('institution_id');
        $status_filter = $request->query('status');

        $regTransactions = PaymentTransaction::with(['application.student', 'application.application_form.institution', 'order'])
            ->whereHas('application.application_form', function ($query) use ($institutionIds, $institution_id) {
                $query->whereIn('institution_id', $institutionIds);
                if ($institution_id) {
                    $query->where('institution_id', $institution_id);
                }
            });

        $admTransactions = AdmissionPaymentTransaction::with(['order.student_application.student', 'order.application_form.institution', 'order'])
            ->whereHas('order.application_form', function ($query) use ($institutionIds, $institution_id) {
                $query->whereIn('institution_id', $institutionIds);
                if ($institution_id) {
                    $query->where('institution_id', $institution_id);
                }
            });

        if ($q) {
            $regTransactions->where(function ($query) use ($q) {
                $query->where('razorpay_payment_id', 'like', "%{$q}%")
                    ->orWhereHas('application.student', function ($qs) use ($q) {
                        $qs->where('full_name', 'like', "%{$q}%")
                            ->orWhere('phone_number', 'like', "%{$q}%");
                    });
            });

            $admTransactions->where(function ($query) use ($q) {
                $query->where('razorpay_payment_id', 'like', "%{$q}%")
                    ->orWhereHas('order.student_application.student', function ($qs) use ($q) {
                        $qs->where('full_name', 'like', "%{$q}%")
                            ->orWhere('phone_number', 'like', "%{$q}%");
                    });
            });
        }

        if ($status_filter) {
            $regTransactions->where('status', $status_filter);
            $status_map = [
                'captured' => 'paid',
                'failed' => 'failed',
                'created' => 'pending'
            ];
            $admTransactions->whereHas('order', function ($query) use ($status_filter, $status_map) {
                $query->where('status', $status_map[$status_filter] ?? $status_filter);
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

        return view('institute.finance.index', compact('transactions', 'institutions', 'totalCollected', 'todayCollected', 'pendingPayments'));
    }

    public function show($type, $id)
    {
        $user = auth()->user();
        $institutionIds = $user->institutions->pluck('id')->toArray();
        $admission_student = null;

        if ($type === 'registration') {
            $transaction = PaymentTransaction::with(['application.student', 'application.application_form.institution', 'order'])
                ->whereHas('application.application_form', function ($q) use ($institutionIds) {
                    $q->whereIn('institution_id', $institutionIds);
                })->findOrFail($id);
            $order = $transaction->order;
            $purpose_title = "Registration for " . ($transaction->application->application_form->application_name ?? 'N/A');
            $student = $transaction->application->student ?? null;
            $application = $transaction->application;
        } elseif ($type === 'admission') {
            $transaction = AdmissionPaymentTransaction::with(['order.student_application.student', 'order.application_form.institution', 'order'])
                ->whereHas('order.application_form', function ($q) use ($institutionIds) {
                    $q->whereIn('institution_id', $institutionIds);
                })->findOrFail($id);
            $order = $transaction->order;
            $purpose_title = "Admission for " . ($order->application_form->application_name ?? 'N/A');
            $student = $order->student_application->student ?? null;
            $application = $order->student_application;

            if ($application) {
                $admission_student = AdmissionStudent::where('student_application_id', $application->id)->first();
            }
        } else {
            abort(404);
        }

        return view('institute.finance.show', compact('transaction', 'order', 'purpose_title', 'student', 'application', 'type', 'admission_student'));
    }

    public function refresh(Request $request)
    {
        $user = auth()->user();
        $institutionIds = $user->institutions->pluck('id')->toArray();

        $request->validate([
            'type' => 'required|in:registration,admission',
            'id' => 'required'
        ]);

        $type = $request->type;
        $id = $request->id;

        if ($type === 'registration') {
            $transaction = PaymentTransaction::with('order')->whereHas('application.application_form', function ($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            })->findOrFail($id);
            $order = $transaction->order;
            $institution = $transaction->application->application_form->institution;
        } else {
            $transaction = AdmissionPaymentTransaction::with('order')->whereHas('order.application_form', function ($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
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
            return back()->with('error', 'Razorpay credentials not configured for this institution.');
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
                    return back()->with('warning', 'No captured payments found for this order on Razorpay.');
                }
            } else {
                return back()->with('error', 'Failed to fetch payment status from Razorpay: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while refreshing status: ' . $e->getMessage());
        }
    }
}
