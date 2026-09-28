<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DebugAdminAccessSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'admin@example.com')->first();
        if (!$user) {
            $this->command->error("User not found");
            return;
        }

        $this->command->info("User ID: " . $user->id);
        
        $org = $user->currentOrganization();
        if ($org) {
            $this->command->info("Current Organization: " . $org->organization_name . " (ID: " . $org->id . ")");
            
            // Check pivot directly
            $pivot = \DB::table('organization_user')
                ->where('user_id', $user->id)
                ->where('organization_id', $org->id)
                ->first();
                
            if ($pivot) {
                $this->command->info("Pivot Exists. Role: " . $pivot->role);
            } else {
                $this->command->error("Pivot Missing!");
            }

            // Check isAdmin
            if ($user->isAdmin($org->id)) {
                $this->command->info("isAdmin check PASSED");
            } else {
                $this->command->error("isAdmin check FAILED");
            }
        } else {
            $this->command->error("No Current Organization returned.");
            // Check raw organizations
            $orgs = $user->organizations;
            $this->command->info("Raw Organizations Count: " . $orgs->count());
            foreach($orgs as $o) {
                $this->command->info("- " . $o->organization_name . " (Pivot Role: " . $o->pivot->role . ")");
            }
        }
    }
}
