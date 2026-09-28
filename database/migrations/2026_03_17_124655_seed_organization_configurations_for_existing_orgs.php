<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // $orgs = \App\Models\Organization::all();
        // foreach ($orgs as $org) {
        //     $config = \App\Models\OrganizationConfiguration::where('organization_id', $org->id)->first();
        //     if (!$config) {
        //         \App\Models\OrganizationConfiguration::create([
        //             'organization_id' => $org->id,
        //             'is_registration_fee_enabled' => true,
        //             'is_admission_enabled' => true,
        //         ]);
        //     }
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
