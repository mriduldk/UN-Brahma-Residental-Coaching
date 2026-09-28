<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Institution;
use App\Models\ApplicationForm;
use Illuminate\Http\Request;

class AdmissionConfigController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $org = $user->currentOrganization();
        
        if (!$org) {
            abort(403, 'No organization found.');
        }

        $institutionId = $request->query('institution_id');
        
        $query = Admission::whereHas('institution', function($q) use ($org) {
                $q->where('organization_id', $org->id);
            })
            ->with(['institution', 'creator', 'application_form'])
            ->latest();

        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }

        $admissions = $query->paginate(10);

        return view('administration.admission_configs.index', compact('admissions'));
    }

    public function create()
    {
        $user = auth()->user();
        $org = $user->currentOrganization();

        if (!$org) {
            abort(403, 'No organization found.');
        }

        $institutions = $org->institutions;

        if ($institutions->isEmpty()) {
            return redirect()->route('administration.institutions.index')
                ->with('error', 'Please create an institution first.');
        }
        
        // Fetch Application Forms grouped by institution
        $applicationForms = ApplicationForm::where('organization_id', $org->id)
            ->select('id', 'institution_id', 'application_name', 'session', 'course_fee')
            ->where('status', 'active')
            ->get()
            ->groupBy('institution_id');

        return view('administration.admission_configs.create', compact('institutions', 'applicationForms'));
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
            
            'allow_dd_full_payment' => 'boolean',
            'dd_full_payment_discount_amount' => 'nullable|numeric|min:0',
            'dd_full_payment_discount_percentage' => 'nullable|numeric|min:0|max:100',
            
            'allow_dd_emi' => 'boolean',
            'dd_emi_tenures' => 'nullable|array',
            'dd_emi_down_payment' => 'nullable|numeric|min:0',
        ]);

        $org = auth()->user()->currentOrganization();
        $institution = Institution::find($request->institution_id);

        if (!$institution || $institution->organization_id !== $org->id) {
            abort(403, 'Invalid institution selected.');
        }

        // Check uniqueness for application_form_id
        if (Admission::where('application_form_id', $request->application_form_id)->exists()) {
             return back()->withErrors(['application_form_id' => 'An admission configuration for this application form already exists.'])->withInput();
        }

        $validated['created_by'] = auth()->id();
        $validated['allow_full_payment'] = $request->has('allow_full_payment');
        $validated['allow_emi'] = $request->has('allow_emi');
        $validated['allow_cash_payment'] = $request->has('allow_cash_payment');
        
        $validated['allow_dd_full_payment'] = $request->has('allow_dd_full_payment');
        $validated['allow_dd_emi'] = $request->has('allow_dd_emi');
        
        if (!$validated['allow_emi']) {
            $validated['emi_tenures'] = null;
            $validated['emi_down_payment'] = null;
        }

        if (!$validated['allow_dd_emi']) {
            $validated['dd_emi_tenures'] = null;
            $validated['dd_emi_down_payment'] = null;
        }

        Admission::create($validated);

        return redirect()->route('administration.admission-configs.index')
            ->with('success', 'Admission configuration created successfully.');
    }

    public function edit(Admission $admissionConfig)
    {
        $user = auth()->user();
        $org = $user->currentOrganization();

        // Security Check
        if ($admissionConfig->institution->organization_id !== $org->id) {
            abort(403, 'Unauthorized access.');
        }

        $institutions = $org->institutions;

        // Fetch Application Forms
        $applicationForms = ApplicationForm::where('organization_id', $org->id)
            ->select('id', 'institution_id', 'application_name', 'session', 'course_fee')
            ->get()
            ->groupBy('institution_id');

        return view('administration.admission_configs.edit', compact('admissionConfig', 'institutions', 'applicationForms'));
    }

    public function update(Request $request, Admission $admissionConfig)
    {
        $org = auth()->user()->currentOrganization();

        // Security Check
        if ($admissionConfig->institution->organization_id !== $org->id) {
            abort(403, 'Unauthorized access.');
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
            
            'allow_dd_full_payment' => 'boolean',
            'dd_full_payment_discount_amount' => 'nullable|numeric|min:0',
            'dd_full_payment_discount_percentage' => 'nullable|numeric|min:0|max:100',
            
            'allow_dd_emi' => 'boolean',
            'dd_emi_tenures' => 'nullable|array',
            'dd_emi_down_payment' => 'nullable|numeric|min:0',
        ]);

        // Re-verify institution
        $institution = Institution::find($request->institution_id);
        if (!$institution || $institution->organization_id !== $org->id) {
             abort(403, 'Invalid institution selected.');
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
        
        $validated['allow_dd_full_payment'] = $request->has('allow_dd_full_payment');
        $validated['allow_dd_emi'] = $request->has('allow_dd_emi');
        
        if (!$validated['allow_emi']) {
            $validated['emi_tenures'] = null;
            $validated['emi_down_payment'] = null;
        }

        if (!$validated['allow_dd_emi']) {
            $validated['dd_emi_tenures'] = null;
            $validated['dd_emi_down_payment'] = null;
        }

        $admissionConfig->update($validated);

        return redirect()->route('administration.admission-configs.index')
            ->with('success', 'Admission configuration updated successfully.');
    }

    public function destroy(Admission $admissionConfig)
    {
        $org = auth()->user()->currentOrganization();

        if ($admissionConfig->institution->organization_id !== $org->id) {
            abort(403, 'Unauthorized access.');
        }

        $admissionConfig->delete();

        return redirect()->route('administration.admission-configs.index')
            ->with('success', 'Admission configuration deleted successfully.');
    }
}
