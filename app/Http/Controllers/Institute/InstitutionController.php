<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Only show institutions mapped to the authenticated user
        $institutions = auth()->user()->institutions()->with(['admissions' => function($q) {
            $q->where('status', 'active')->with('application_form')->latest();
        }])->latest()->get();
        return view('institute.institutions.index', compact('institutions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Usually, Institute Users might not create new institutions, 
        // but keeping it as per 'exactly like administration' request.
        // We will just return the view.
        return view('institute.institutions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'institution_name' => 'required|string|max:255',
            'institution_code' => 'required|string|max:50|unique:institutions,institution_code',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:100',
            'bank_ifsc_code' => 'nullable|string|max:20',
            'bank_branch_name' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $org = $user->currentOrganization();
        
        if (!$org) {
            abort(403, 'No organization found');
        }

        $institution = new Institution($request->all());
        $institution->organization_id = $org->id;
        $institution->save();

        // Automatically attach the creator as an admin of this institution
        $institution->users()->attach($user->id, ['role' => 'admin']);

        return redirect()->route('institute.institutions.index')
            ->with('success', 'Institution created and assigned to you successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution)
    {
        // Enforce mapping check
        if (!auth()->user()->institutions()->where('institutions.id', $institution->id)->exists()) {
            abort(403, 'Access Denied');
        }
        
        // Note: View might be missing if administration also missed it, but logic is here.
        return view('administration.institutions.show', compact('institution')); 
        // Or create institute.institutions.show if needed. 
        // For now referring to admin's view or maybe we should create a copy.
        // Given 'exactly like', likely no show view exists if unrelated to forms.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institution $institution)
    {
        // Enforce mapping check
        if (!auth()->user()->institutions()->where('institutions.id', $institution->id)->exists()) {
            abort(403, 'Access Denied');
        }

        return view('institute.institutions.edit', compact('institution'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institution $institution)
    {
        // Enforce mapping check and Admin Role within Institution context
        if (!auth()->user()->isInstitutionAdmin($institution->id)) {
            abort(403, 'Only Institution Admins can update details.');
        }

        $request->validate([
            'institution_name' => 'required|string|max:255',
            'institution_code' => 'required|string|max:50|unique:institutions,institution_code,' . $institution->id,
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:100',
            'bank_ifsc_code' => 'nullable|string|max:20',
            'bank_branch_name' => 'nullable|string|max:100',
        ]);

        $institution->update($request->all());

        return redirect()->route('institute.institutions.index')
            ->with('success', 'Institution updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        // Enforce mapping check and Admin Role within Institution context
        if (!auth()->user()->isInstitutionAdmin($institution->id)) {
            abort(403, 'Only Institution Admins can delete institutions.');
        }

        $institution->delete();

        return redirect()->route('institute.institutions.index')
            ->with('success', 'Institution deleted successfully.');
    }
}
