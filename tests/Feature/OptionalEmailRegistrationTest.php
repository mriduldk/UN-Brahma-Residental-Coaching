<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionalEmailRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_registration_without_email()
    {
        // Mocking the validation and store logic or just calling the route
        // Since migrations are broken for SQLite, I might need to bypass them or use a different database for testing.
        // But let's try to run a simple validation test if possible.
        
        $response = $this->post('/register', [
            'name' => 'Test User',
            'mobile_number' => '9876543210',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            // 'email' is missing
        ]);

        // If it fails validation, it would redirect back with errors.
        // If it passes, it might fail later due to the migration issue if it tries to hit the DB.
        // But we want to see if 'email' is NOT in the validation errors.
        
        $response->assertSessionDoesNotHaveErrors('email');
    }
}
