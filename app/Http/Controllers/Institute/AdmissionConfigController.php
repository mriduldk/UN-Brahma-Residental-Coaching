<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Institution;
use Illuminate\Http\Request;

class AdmissionConfigController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $institutionId = $request->query('institution_id');
        
        $query = Admission::whereIn('institution_id', $user->institutions->pluck('id'))
            ->with(['institution', 'creator', 'application_form'])
            ->latest();

        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }

        $admissions = $query->paginate(10);

        return view('institute.admission_configs.index', compact('admissions'));
    }

    public function create()
    {
        $user = auth()->user();
        $institutions = $user->institutions()->wherePivot('role', 'admin')->get();

        if ($institutions->isEmpty()) {
            abort(403, 'You do not have permission to manage admission configurations for any institution.');
        }
        
        // Fetch Application Forms
        $applicationForms = \App\Models\ApplicationForm::whereIn('institution_id', $institutions->pluck('id'))
            ->select('id', 'institution_id', 'application_name', 'session', 'course_fee')
            ->where('status', 'active')
            ->get()
            ->groupBy('institution_id');

        return view('institute.admission_configs.create', compact('institutions', 'applicationForms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'application_form_id' => 'required|exists:application_forms,id',
            'academic_session' => 'required|string',
            'admission_fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            
            'allow_full_payment' => 'boolean',
            'full_payment_discount_amount' => 'nullable|numeric|min:0',
            'full_payment_discount_percentage' => 'nullable|numeric|min:0|max:100',
            
            'allow_emi' => 'boolean',
            'emi_tenures' => 'nullable|array',
            'emi_down_payment' => 'nullable|numeric|min:0',
            
            'allow_cash_payment' => 'boolean',
            'cash_payment_days' => 'nullable|integer|min:0',
        ]);

        // Security Check: User must own the institution and be admin
        if (!auth()->user()->isInstitutionAdmin($request->institution_id)) {
            abort(403, 'Unauthorized action: Admin access required.');
        }

        // Check uniqueness for application_form_id
        if (Admission::where('application_form_id', $request->application_form_id)->exists()) {
             return back()->withErrors(['application_form_id' => 'An admission configuration for this application form already exists.'])->withInput();
        }

        $validated['created_by'] = auth()->id();
        $validated['allow_full_payment'] = $request->has('allow_full_payment');
        $validated['allow_emi'] = $request->has('allow_emi');
        $validated['allow_cash_payment'] = $request->has('allow_cash_payment');
        
        // Sanitize arrays if needed, though 'array' validation helps
        if (!$validated['allow_emi']) {
            $validated['emi_tenures'] = null;
            $validated['emi_down_payment'] = null;
        }

        Admission::create($validated);

        return redirect()->route('institute.admission-configs.index')
            ->with('success', 'Admission configuration created successfully.');
    }

    public function edit(Admission $admissionConfig)
    {
        // Security Check
        if (!auth()->user()->isInstitutionAdmin($admissionConfig->institution_id)) {
            abort(403, 'Unauthorized action: Admin access required.');
        }

        $user = auth()->user();
        $institutions = $user->institutions;

        // Fetch Application Forms
        $applicationForms = \App\Models\ApplicationForm::whereIn('institution_id', $institutions->pluck('id'))
            ->select('id', 'institution_id', 'application_name', 'session', 'course_fee')
            ->get()
            ->groupBy('institution_id');

        return view('institute.admission_configs.edit', compact('admissionConfig', 'institutions', 'applicationForms'));
    }

    public function update(Request $request, Admission $admissionConfig)
    {
        // Security Check
        if (!auth()->user()->isInstitutionAdmin($admissionConfig->institution_id)) {
            abort(403, 'Unauthorized action: Admin access required.');
        }

        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'application_form_id' => 'required|exists:application_forms,id',
            'academic_session' => 'required|string',
            'admission_fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            
            'allow_full_payment' => 'boolean',
            'full_payment_discount_amount' => 'nullable|numeric|min:0',
            'full_payment_discount_percentage' => 'nullable|numeric|min:0|max:100',
            
            'allow_emi' => 'boolean',
            'emi_tenures' => 'nullable|array',
            'emi_down_payment' => 'nullable|numeric|min:0',
            
            'allow_cash_payment' => 'boolean',
            'cash_payment_days' => 'nullable|integer|min:0',
        ]);

        // Re-verify institution ownership if changed
        if (!auth()->user()->isInstitutionAdmin($request->institution_id)) {
             abort(403, 'Unauthorized action: Admin access required.');
        }

        // Check uniqueness if application_form_id changed
        if ($admissionConfig->application_form_id != $request->application_form_id) {
            if (Admission::where('application_form_id', $request->application_form_id)->exists()) {
                 return back()->withErrors(['application_form_id' => 'An admission configuration for this application form already exists.'])->withInput();
            }
        }

        $validated['allow_full_payment'] = $request->has('allow_full_payment');
        $validated['allow_emi'] = $request->has('allow_emi');
        $validated['allow_cash_payment'] = $request->has('allow_cash_payment');
        
        if (!$validated['allow_emi']) {
            $validated['emi_tenures'] = null;
            $validated['emi_down_payment'] = null;
        }

        $admissionConfig->update($validated);

        return redirect()->route('institute.admission-configs.index')
            ->with('success', 'Admission configuration updated successfully.');
    }

    public function destroy(Admission $admissionConfig)
    {
        if (!auth()->user()->isInstitutionAdmin($admissionConfig->institution_id)) {
            abort(403, 'Unauthorized action: Admin access required.');
        }

        $admissionConfig->delete();

        return redirect()->route('institute.institutions.index')
            ->with('success', 'Admission configuration deleted successfully.');
    }
}
