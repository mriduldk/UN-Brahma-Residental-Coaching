<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\Institution;
use App\Models\Student;
use App\Models\User;
use App\Models\PaymentTransaction;
use App\Models\AdmissionPaymentTransaction;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrganizations = Organization::count();
        $totalInstitutions = Institution::count();
        $totalStudents = Student::count();

        // Total Revenue across the entire platform
        $regRevenue = PaymentTransaction::where('status', 'captured')->sum('amount');
        $admRevenue = AdmissionPaymentTransaction::where('status', 'captured')->sum('amount');
        
        $totalRevenue = $regRevenue + $admRevenue;

        // Recent Organizations
        $recentOrganizations = Organization::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalOrganizations',
            'totalInstitutions',
            'totalStudents',
            'totalRevenue',
            'recentOrganizations'
        ));
    }

    public function leadTracking(Request $request)
    {
        $query = Organization::with(['user', 'leadTracking'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('organization_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%")
                         ->orWhere('mobile_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('business_type')) {
            $query->where('business_type', $request->business_type);
        }

        if ($request->filled('call_status')) {
            $status = $request->call_status;
            if ($status === 'uncontacted') {
                $query->where(function($q) {
                    $q->doesntHave('leadTracking')
                      ->orWhereHas('leadTracking', function($lq) {
                          $lq->whereNull('call_status');
                      });
                });
            } else {
                $query->whereHas('leadTracking', function($q) use ($status) {
                    $q->where('call_status', $status);
                });
            }
        }

        if ($request->filled('lead_temperature')) {
            $temp = $request->lead_temperature;
            $query->whereHas('leadTracking', function($q) use ($temp) {
                $q->where('lead_temperature', $temp);
            });
        }

        $organizations = $query->paginate(20)->withQueryString();
        
        return view('admin.lead_tracking', compact('organizations'));
    }

    public function updateTracking(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'call_status' => 'nullable|string',
            'lead_temperature' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $tracking = \App\Models\OrganizationLeadTracking::firstOrNew(['organization_id' => $request->organization_id]);

        if ($request->has('call_status')) $tracking->call_status = $request->call_status;
        if ($request->has('lead_temperature')) $tracking->lead_temperature = $request->lead_temperature;
        if ($request->has('remarks')) $tracking->remarks = $request->remarks;
        
        $tracking->last_called_by = auth()->id();
        $tracking->last_called_at = now();
        $tracking->save();

        return response()->json([
            'success' => true,
            'message' => 'Tracking updated successfully',
            'tracking' => $tracking
        ]);
    }

    public function impersonateUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->get('search') != '') {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->get('role') != '') {
            $query->where('role', $request->get('role'));
        }

        // Exclude the current super admin
        $query->where('id', '!=', auth()->id());

        $users = $query->orderBy('role')->paginate(20);

        return view('admin.users.impersonate', compact('users'));
    }

    public function impersonate($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot impersonate another admin.');
        }

        // Store the original admin ID in the session
        session()->put('impersonator_id', auth()->id());

        auth()->login($user);

        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}.");
    }

    public function leaveImpersonation()
    {
        if (session()->has('impersonator_id')) {
            $adminId = session()->pull('impersonator_id');
            $admin = User::find($adminId);

            if ($admin) {
                auth()->login($admin);
                return redirect()->route('admin.dashboard')->with('success', 'Returned to Admin session.');
            }
        }

        return redirect()->route('dashboard')->with('error', 'Could not return to original session.');
    }
}
