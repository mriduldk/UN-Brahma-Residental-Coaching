<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class InstitutionUserController extends Controller
{
    public function index($id)
    {
        $institution = Institution::with('users')->findOrFail($id);
        
        $this->authorizeAccess($institution);
        
        if (request()->routeIs('institute.*')) {
            return view('institute.institutions.users.index', compact('institution'));
        }

        return view('administration.institutions.users.index', compact('institution'));
    }

    public function store($id, Request $request)
    {
        $institution = Institution::findOrFail($id);
        $this->authorizeAccess($institution);

        $request->validate([
            'email' => 'nullable|email',
            'role' => ['required', Rule::in(['admin', 'viewer'])],
            'name' => 'nullable|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Create user if not exists
            $user = User::create([
                'name' => $request->name ?? Str::before($request->email, '@'),
                'email' => $request->email,
                'password' => Hash::make('@Password$$4465**^'), // Default password
                'role' => 'institute',
            ]);
            
            // Also attach to the main organization if not already
            // Assuming current user's org is the context
            $org = auth()->user()->currentOrganization();
            if ($org && !$user->organizations()->where('organization_id', $org->id)->exists()) {
                $user->organizations()->attach($org->id, ['role' => 'viewer']);
            }
        }

        // Attach to institution
        if (!$institution->users()->where('user_id', $user->id)->exists()) {
            $institution->users()->attach($user->id, ['role' => $request->role]);
            return back()->with('success', 'User added to institution successfully.');
        }

        return back()->with('error', 'User is already added to this institution.');
    }

    public function update($id, $userId, Request $request)
    {
        $institution = Institution::findOrFail($id);
        $this->authorizeAccess($institution);

        $request->validate([
            'role' => ['required', Rule::in(['admin', 'viewer'])],
        ]);

        $institution->users()->updateExistingPivot($userId, ['role' => $request->role]);

        return back()->with('success', 'User role updated successfully.');
    }

    public function destroy($id, $userId)
    {
        $institution = Institution::findOrFail($id);
        $this->authorizeAccess($institution);

        $institution->users()->detach($userId);

        return back()->with('success', 'User removed from institution.');
    }

    private function authorizeAccess($institution)
    {
        $user = auth()->user();
        if (!$user) abort(403);

        $org = $user->currentOrganization();
        
        // Allow if Org Admin OR Institution Admin
        $isOrgAdmin = $org && $user->isAdmin($org->id) && $institution->organization_id == $org->id;
        $isInstAdmin = $user->isInstitutionAdmin($institution->id);

        if (!$isOrgAdmin && !$isInstAdmin) {
            abort(403, 'Unauthorized access to institution user management.');
        }
    }
}
