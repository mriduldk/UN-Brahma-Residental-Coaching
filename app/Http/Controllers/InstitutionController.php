<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $org = auth()->user()->currentOrganization();
        $institutions = $org ? $org->institutions()->with(['admissions' => function($q) {
            $q->where('status', 'active')->with('application_form')->latest();
        }])->latest()->get() : collect([]); // Safe check
        return view('administration.institutions.index', compact('institutions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $org = $user->currentOrganization();
        
        \Log::info("Institution Create Debug", [
            'user_id' => $user->id,
            'org_id' => $org ? $org->id : 'null',
            'is_admin' => $org ? $user->isAdmin($org->id) : 'N/A',
            'pivot_roles' => $org ? $user->organizations()->where('organization_id', $org->id)->get()->pluck('pivot.role') : []
        ]);

        if (!$org) {
             abort(403, 'No organization mapped.');
        }

        if (!$user->isAdmin($org->id)) {
            abort(403, 'Only Admins can create institutions. (Debug: User ' . $user->id . ' is not admin of Org ' . $org->id . ')');
        }
        return view('administration.institutions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org) abort(403, 'No organization found');
        
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only Admins can create institutions.');
        }
        
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
            'razorpay_key' => 'nullable|string',
            'razorpay_secret' => 'nullable|string',
        ]);

        $institution = new Institution($request->all());
        $institution->organization_id = $org->id;
        $institution->save();

        return redirect()->route('administration.institutions.index')
            ->with('success', 'Institution created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution)
    {
        // Ensure the institution belongs to the authenticated user's organization
        $org = auth()->user()->currentOrganization();
        if (!$org || $institution->organization_id !== $org->id) {
            abort(403);
        }
        return view('administration.institutions.show', compact('institution'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institution $institution)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org || $institution->organization_id !== $org->id) {
            abort(403);
        }
        
        // Check Admin Role
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only Admins can edit institutions.');
        }
        
        return view('administration.institutions.edit', compact('institution'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institution $institution)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org || $institution->organization_id !== $org->id) {
            abort(403);
        }

        // Check Admin Role
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only Admins can update institutions.');
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
            'razorpay_key' => 'nullable|string',
            'razorpay_secret' => 'nullable|string',
        ]);

        $institution->update($request->all());

        return redirect()->route('administration.institutions.index')
            ->with('success', 'Institution updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        $org = auth()->user()->currentOrganization();
        if (!$org || $institution->organization_id !== $org->id) {
            abort(403);
        }

        // Check Admin Role
        if (!auth()->user()->isAdmin($org->id)) {
            abort(403, 'Only Admins can delete institutions.');
        }

        $institution->delete();

        return redirect()->route('administration.institutions.index')
            ->with('success', 'Institution deleted successfully.');
    }
}
