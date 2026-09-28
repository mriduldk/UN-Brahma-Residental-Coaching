<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\User;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organizations = Organization::with('users', 'institutions')->latest()->paginate(15);
        return view('admin.organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all(); // Potential owners
        return view('admin.organizations.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'type' => 'required|string',
            'category' => 'nullable|string',
            'industry' => 'nullable|string',
            'registration_no' => 'nullable|string',
            'gst_no' => 'nullable|string',
            'pan_no' => 'nullable|string',
            'tan_no' => 'nullable|string',
            'website' => 'nullable|url',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'nullable|string|email|max:255|unique:users,email',
            'admin_phone' => 'nullable|string|max:20',
            'admin_password' => 'required|string|min:8',
        ]);

        // Create the new user
        $user = User::create([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'phone' => $validated['admin_phone'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['admin_password']),
            'role' => 'administration', 
        ]);

        $orgData = [
            'organization_name' => $validated['organization_name'],
            'short_name' => $validated['short_name'],
            'type' => $validated['type'],
            'category' => $validated['category'] ?? null,
            'industry' => $validated['industry'] ?? null,
            'registration_no' => $validated['registration_no'] ?? null,
            'gst_no' => $validated['gst_no'] ?? null,
            'pan_no' => $validated['pan_no'] ?? null,
            'tan_no' => $validated['tan_no'] ?? null,
            'website' => $validated['website'] ?? null,
            'user_id' => $user->id,
        ];

        $org = Organization::create($orgData);
        
        // Attach the assigned user as an admin to the new organization
        $user->organizations()->attach($org->id, ['role' => 'admin']);

        return redirect()->route('admin.organizations.index')->with('success', 'Organization and Primary Administrator created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $organization = Organization::with([
                'user', 
                'institutions.users',
                'institutions.application_forms.student_applications.student',
                'institutions.application_forms.student_applications.admission_student'
            ])
            ->findOrFail($id);
            
        $institutionIds = $organization->institutions->pluck('id');

        // Admission Configs
        $admissionConfigs = \App\Models\Admission::whereIn('institution_id', $institutionIds)->get();

        // Applications
        $totalApplications = \App\Models\StudentApplication::whereHas('application_form', function($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->count();

        $pendingApplications = \App\Models\StudentApplication::whereHas('application_form', function($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'submitted')->count();

        $approvedApplications = \App\Models\StudentApplication::whereHas('application_form', function($q) use ($institutionIds) {
            $q->whereIn('institution_id', $institutionIds);
        })->where('status', 'approved')->count();

        // Revenue
        $regRevenue = \App\Models\PaymentTransaction::where('status', 'captured')
            ->whereHas('application.application_form', function($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            })->sum('amount');

        $admRevenue = \App\Models\AdmissionPaymentTransaction::where('status', 'captured')
            ->whereHas('order.student_application.application_form', function($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            })->sum('amount');

        $totalRevenue = $regRevenue + $admRevenue;

        // EMI & Cash
        $admissionsData = \App\Models\AdmissionStudent::whereIn('institution_id', $institutionIds)->get();
        $totalPendingEmi = 0;
        $unsettledCash = 0;

        foreach ($admissionsData as $adm) {
            if ($adm->payment_type === 'emi') {
                $totalPendingEmi += $adm->pending_amount;
            }
            $unsettledCash += \App\Models\AdmissionPayment::where('student_application_id', $adm->student_application_id)
                ->where('payment_type', 'cash')
                ->where('is_settled', false)
                ->sum('paid_amount');
        }

        $applicationForms = \App\Models\ApplicationForm::where('organization_id', $organization->id)
            ->orWhereIn('institution_id', $institutionIds)
            ->get();

        $recentApplications = \App\Models\StudentApplication::with(['student', 'application_form'])
            ->whereHas('application_form', function($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('admin.organizations.show', compact(
            'organization', 
            'admissionConfigs',
            'applicationForms',
            'recentApplications',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'totalRevenue',
            'totalPendingEmi',
            'unsettledCash'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $organization = Organization::findOrFail($id);
        $users = User::all();
        return view('admin.organizations.edit', compact('organization', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $organization = Organization::findOrFail($id);
        
        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'type' => 'required|string',
            'category' => 'nullable|string',
            'industry' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'registration_no' => 'nullable|string',
            'gst_no' => 'nullable|string',
            'pan_no' => 'nullable|string',
            'tan_no' => 'nullable|string',
            'website' => 'nullable|url',
            'organization_code' => 'required|string|unique:organizations,organization_code,'.$organization->id,
        ]);

        $oldUserId = $organization->user_id;

        $organization->update($validated);

        // If the primary user changed, ensure the new user has admin access
        if ($oldUserId != $validated['user_id']) {
            $newUser = User::findOrFail($validated['user_id']);
            if (!$newUser->organizations()->where('organization_id', $organization->id)->exists()) {
                $newUser->organizations()->attach($organization->id, ['role' => 'admin']);
            }
        }

        return redirect()->route('admin.organizations.index')->with('success', 'Organization updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();
        
        return redirect()->route('admin.organizations.index')->with('success', 'Organization deleted successfully!');
    }
}
