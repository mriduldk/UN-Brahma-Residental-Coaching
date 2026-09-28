<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Support\Facades\Hash;

class InstituteUserSeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure an Institution exists
        $institution = Institution::first();
        if (!$institution) {
            // Need an org first?
            $user = User::first(); // Assuming admin exists
            $org = \App\Models\Organization::firstOrCreate(
                ['organization_code' => 'ORG001'],
                [
                    'user_id' => $user ? $user->id : 1, // Fallback
                    'organization_name' => 'Demo Org',
                    'short_name' => 'DO',
                    'type' => 'Govt',
                ]
            );

            $institution = Institution::create([
                'organization_id' => $org->id,
                'institution_name' => 'Demo Institute',
                'institution_code' => 'INST001',
                'contact_number' => '1234567890',
                'email' => 'demo@institute.com',
                'address' => 'Demo Address',
            ]);
        }

        // 2. Create Institute User
        $user = User::updateOrCreate(
            ['email' => 'institute@example.com'],
            [
                'name' => 'Institute Admin',
                'password' => Hash::make('password'),
                'role' => 'institute',
            ]
        );

        // 3. Map User to Institution
        // Check if map exists
        if (!$user->institutions()->where('institution_id', $institution->id)->exists()) {
            $user->institutions()->attach($institution->id, ['role' => 'admin']);
            $this->command->info('Mapped user to institution.');
        } else {
            $this->command->info('User already mapped.');
        }

        $this->command->info('Institute User: institute@example.com / password');
    }
}
