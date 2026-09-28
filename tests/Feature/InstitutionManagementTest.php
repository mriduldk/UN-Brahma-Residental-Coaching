<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an administration user
        $this->user = User::factory()->create([
            'role' => 'administration',
        ]);

        // Create an organization associated with the user
        $this->organization = Organization::create([
            'user_id' => $this->user->id,
            'organization_name' => 'Test Organization',
            'short_name' => 'TESTORG',
            'organization_code' => 'ORG-001',
            'type' => 'Private',
            'category' => 'Training',
        ]);
    }

    public function test_administration_can_view_institutions_list()
    {
        $response = $this->actingAs($this->user)
            ->get(route('administration.institutions.index'));

        $response->assertStatus(200);
        $response->assertSee('Institutions');
    }

    public function test_administration_can_create_institution()
    {
        $response = $this->actingAs($this->user)
            ->post(route('administration.institutions.store'), [
                'institution_name' => 'Test Institution',
                'institution_code' => 'INST-001',
                'email' => 'test@inst.com',
                'contact_number' => '1234567890',
            ]);

        $response->assertRedirect(route('administration.institutions.index'));
        $this->assertDatabaseHas('institutions', [
            'institution_name' => 'Test Institution',
            'institution_code' => 'INST-001',
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_administration_can_edit_institution()
    {
        $institution = Institution::create([
            'organization_id' => $this->organization->id,
            'institution_name' => 'Old Name',
            'institution_code' => 'OLD-001',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('administration.institutions.update', $institution->id), [
                'institution_name' => 'New Name',
                'institution_code' => 'OLD-001', // Keep same code
            ]);

        $response->assertRedirect(route('administration.institutions.index'));
        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id,
            'institution_name' => 'New Name',
        ]);
    }

    public function test_administration_can_delete_institution()
    {
        $institution = Institution::create([
            'organization_id' => $this->organization->id,
            'institution_name' => 'To Delete',
            'institution_code' => 'DEL-001',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('administration.institutions.destroy', $institution->id));

        $response->assertRedirect(route('administration.institutions.index'));
        $this->assertDatabaseMissing('institutions', [
            'id' => $institution->id,
        ]);
    }
    
    public function test_cannot_access_other_organization_institution()
    {
        // Another user and organization
        $otherUser = User::factory()->create(['role' => 'administration']);
        $otherOrg = Organization::create([
            'user_id' => $otherUser->id,
            'organization_name' => 'Other Org',
            'short_name' => 'OTHER',
            'organization_code' => 'ORG-002',
            'type' => 'Private', 
            'category' => 'Training'
        ]);
        
        $institution = Institution::create([
            'organization_id' => $otherOrg->id,
            'institution_name' => 'Other Inst',
            'institution_code' => 'OTHER-001',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('administration.institutions.edit', $institution->id));

        $response->assertStatus(403);
    }
}
