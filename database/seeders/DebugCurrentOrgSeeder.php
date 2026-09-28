<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DebugCurrentOrgSeeder extends Seeder
{
    public function run()
    {
        $userId = 6;
        $user = User::find($userId);
        
        if (!$user) {
            $this->command->error("User 6 not found");
            return;
        }

        $this->command->info("User: {$user->name}");
        
        $currentOrg = $user->currentOrganization();
        if ($currentOrg) {
            $this->command->info("Current Organization (first()): {$currentOrg->organization_name} (ID: {$currentOrg->id})");
            $isAdmin = $user->isAdmin($currentOrg->id);
            $this->command->info("Is Admin for Current Org? " . ($isAdmin ? 'YES' : 'NO'));
        } else {
            $this->command->error("No Current Organization found.");
        }

        $this->command->info("--- All Mapped Organizations ---");
        foreach($user->organizations as $org) {
            $this->command->info("ID: {$org->id} | Name: {$org->organization_name} | Role: {$org->pivot->role}");
        }
    }
}
