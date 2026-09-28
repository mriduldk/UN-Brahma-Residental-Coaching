<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $institutions = $user->institutions()->withCount(['application_forms'])->get();
        $institutionIds = $institutions->pluck('id');

        // Stats
        $totalApplications = \App\Models\StudentApplication::whereHas('application_form', function ($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->count();

        $pendingReview = \App\Models\StudentApplication::whereHas('application_form', function ($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'submitted')->count();

        // Revenue (sum of payments for these applications)
        $totalRevenue = \App\Models\AdmissionPayment::whereHas('student_application.application_form', function ($q) use ($institutionIds) {
             $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'success')->sum('paid_amount');

        // Detailed Financial Metrics
        $admissions = \App\Models\AdmissionStudent::whereIn('institution_id', $institutionIds)->get();
        
        $totalPendingEmi = 0;
        $upcomingDuesCount = 0;
        $unsettledCash = 0;
        $nextWeek = now()->addDays(7);

        foreach ($admissions as $adm) {
            if ($adm->payment_type === 'emi') {
                $totalPendingEmi += $adm->pending_amount;
                
                if ($adm->next_payment_date && $adm->pending_amount > 0) {
                    $dueDate = \Carbon\Carbon::parse($adm->next_payment_date);
                    if ($dueDate->isPast() || $dueDate->lessThanOrEqualTo($nextWeek)) {
                        $upcomingDuesCount++;
                    }
                }
            }

            // Unsettled cash from payments linked to these admissions
            $unsettledCash += \App\Models\AdmissionPayment::where('student_application_id', $adm->student_application_id)
                ->where('payment_type', 'cash')
                ->where('is_settled', false)
                ->sum('paid_amount');
        }
        
        // Recent Activity (Applications)
        $recentApplications = \App\Models\StudentApplication::with(['application_student', 'application_form'])
            ->whereHas('application_form', function ($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('institute.dashboard', compact(
            'institutions', 
            'totalApplications', 
            'pendingReview', 
            'totalRevenue', 
            'totalPendingEmi', 
            'upcomingDuesCount', 
            'unsettledCash', 
            'recentApplications'
        ));
    }
}
