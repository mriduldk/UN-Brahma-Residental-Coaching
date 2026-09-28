<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentApplication;

class StudentApplicationController extends Controller
{
    /**
     * Display a comprehensive overview of a specific student's application.
     */
    public function show(string $id)
    {
        $application = StudentApplication::with([
            'student',
            'application_form.institution.organization',
            'application_address',
            'application_parent',
            'application_academic_qualifications',
            'application_documents',
            'application_bank_detail',
            'admission_student',
            'admission_payments',
            'payment_transactions',
            'admission_payment_transactions'
        ])->findOrFail($id);

        return view('admin.student-applications.show', compact('application'));
    }
}
