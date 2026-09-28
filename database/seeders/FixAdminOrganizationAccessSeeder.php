<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;

class FixAdminOrganizationAccessSeeder extends Seeder
{
    public function run()
    {
        // Find the admin user
        $user = User::where('email', 'admin@example.com')->first();
        if (!$user) {
            $this->command->error("Admin user not found.");
            return;
        }

        // Find the organization owned by this user (legacy check)
        $org = Organization::where('user_id', $user->id)->first();
        
        if (!$org) {
            // Fallback: Find ANY organization if legacy ownership is missing
            $org = Organization::first();
        }

        if ($org) {
            // Sync without detaching to ensure mapping exists
            $user->organizations()->syncWithoutDetaching([
                $org->id => ['role' => 'admin']
            ]);
            $this->command->info("Synced Admin ({$user->email}) to Organization ({$org->organization_name}) with role 'admin'.");
        } else {
            $this->command->error("No organization found to map.");
        }
    }
}
