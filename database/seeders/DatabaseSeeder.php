<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'mobile_number' => '9876543210',
                'mobile_verified_at' => now(),
                'password' => bcrypt('@Password$$4465**^'), 
                'role' => 'admin',
                'remember_token' => Str::random(10),
            ]
        );

        // Administration
        \App\Models\User::firstOrCreate(
            ['email' => 'administration@example.com'],
            [
                'name' => 'Administration User',
                'email_verified_at' => now(),
                'mobile_number' => '9876543211',
                'mobile_verified_at' => now(),
                'password' => bcrypt('@Password$$4465**^'),
                'role' => 'administration',
                'remember_token' => Str::random(10),
            ]
        );

        // Student
        \App\Models\User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'email_verified_at' => now(),
                'mobile_number' => '9876543212',
                'mobile_verified_at' => now(),
                'password' => bcrypt('@Password$$4465**^'),
                'role' => 'student',
                'remember_token' => Str::random(10),
            ]
        );

        // Institute
        \App\Models\User::firstOrCreate(
            ['email' => 'institute@example.com'],
            [
                'name' => 'Institute User',
                'email_verified_at' => now(),
                'mobile_number' => '9876543213',
                'mobile_verified_at' => now(),
                'password' => bcrypt('@Password$$4465**^'),
                'role' => 'institute',
                'remember_token' => Str::random(10),
            ]
        );

        $this->call(BtclaConstituencySeeder::class);
    }
}
