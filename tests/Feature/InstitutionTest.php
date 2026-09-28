<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InstitutionTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_institution_creation_with_encrypted_razorpay_details(): void
    {
        $user = \App\Models\User::factory()->create();
        
        $organization = \App\Models\Organization::create([
            'user_id' => $user->id,
            'organization_name' => 'Test Org',
            'short_name' => 'TO',
            'organization_code' => 'TO-001'.rand(100,999),
            'type' => 'Private',
            'category' => 'Education',
            'industry' => 'Education',
        ]);

        $razorpayKey = 'rzp_test_1234567890';
        $razorpaySecret = 'secret_1234567890';

        $institution = \App\Models\Institution::create([
            'organization_id' => $organization->id,
            'institution_name' => 'Test Institution',
            'institution_code' => 'INST-001',
            'address' => '123 Test St',
            'bank_account_no' => '1234567890',
            'razorpay_key' => $razorpayKey,
            'razorpay_secret' => $razorpaySecret,
        ]);

        // Check if institution was created
        $this->assertDatabaseHas('institutions', [
            'institution_name' => 'Test Institution',
            'institution_code' => 'INST-001',
        ]);

        // Verify Razorpay details are encrypted in DB
        $rawInstitution = \Illuminate\Support\Facades\DB::table('institutions')->where('id', $institution->id)->first();
        $this->assertNotEquals($razorpayKey, $rawInstitution->razorpay_key);
        $this->assertNotEquals($razorpaySecret, $rawInstitution->razorpay_secret);
        
        // However, Laravel encrypts them, so they should be much longer and definitely different.
        // Also they should be decrypted when accessed via model
        $retrievedInstitution = \App\Models\Institution::find($institution->id);
        $this->assertEquals($razorpayKey, $retrievedInstitution->razorpay_key);
        $this->assertEquals($razorpaySecret, $retrievedInstitution->razorpay_secret);
    }
}
