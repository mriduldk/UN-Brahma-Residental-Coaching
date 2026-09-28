<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;

class InstitutionController extends Controller
{
    /**
     * Display a comprehensive overview of an institution.
     */
    public function show(string $id)
    {
        $institution = Institution::with([
            'organization', 
            'application_forms', 
            'users'
        ])->findOrFail($id);

        // Fetch related analytical data for this specific institution
        $totalForms = $institution->application_forms->count();
        $institutionId = $institution->id;

        $totalStudents = \App\Models\StudentApplication::whereHas('application_form', function($q) use ($institutionId) {
            $q->where('institution_id', $institutionId);
        })->count();
        
        $totalRevenue = \App\Models\AdmissionPaymentTransaction::where('status', 'captured')
            ->whereHas('order.student_application.application_form', function($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            })->sum('amount');

        return view('admin.institutions.show', compact('institution', 'totalForms', 'totalStudents', 'totalRevenue'));
    }
}
