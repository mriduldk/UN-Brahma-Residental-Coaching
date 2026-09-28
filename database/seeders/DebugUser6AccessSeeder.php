<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;

class DebugUser6AccessSeeder extends Seeder
{
    public function run()
    {
        $userId = 6;
        $orgId = 2;

        $user = User::find($userId);
        if (!$user) {
            $this->command->error("User $userId not found.");
            return;
        }
        $this->command->info("User: " . $user->name . " (" . $user->email . ")");

        $org = Organization::find($orgId);
        if (!$org) {
            $this->command->error("Organization $orgId not found.");
            return;
        }
        $this->command->info("Organization: " . $org->organization_name);

        // Check Pivot
        $pivot = \DB::table('organization_user')
            ->where('user_id', $userId)
            ->where('organization_id', $orgId)
            ->first();

        if ($pivot) {
            $this->command->info("Pivot found. Role: " . $pivot->role);
        } else {
            $this->command->error("Pivot record MISSING for User $userId and Org $orgId.");
            
            // Auto-fix if missing
            $user->organizations()->attach($orgId, ['role' => 'admin']);
            $this->command->info("Created pivot with role 'admin'.");
        }

        // Check isAdmin
        if ($user->isAdmin($orgId)) {
             $this->command->info("isAdmin($orgId) PASSED.");
        } else {
             $this->command->error("isAdmin($orgId) FAILED.");
        }
    }
}
