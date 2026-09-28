<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationForm;

class ApplicationFormController extends Controller
{
    /**
     * Display a comprehensive overview of a specific form instance.
     */
    public function show(string $id)
    {
        $form = ApplicationForm::with([
            'institution.organization',
            'student_applications' => function($q) {
                $q->latest();
            },
            'student_applications.student',
            'student_applications.admission_student'
        ])->findOrFail($id);

        $approvedCount = $form->student_applications->where('status', 'approved')->count();
        $submittedCount = $form->student_applications->where('status', 'submitted')->count();
        $rejectedCount = $form->student_applications->where('status', 'rejected')->count();

        // Total Pending EMI from students enrolled via this form specifically
        $totalPendingEmi = 0;
        foreach($form->student_applications as $app) {
           if ($app->admission_student && $app->admission_student->payment_type === 'emi') {
               $totalPendingEmi += $app->admission_student->pending_amount;
           }
        }

        return view('admin.forms.show', compact('form', 'approvedCount', 'submittedCount', 'rejectedCount', 'totalPendingEmi'));
    }
}
