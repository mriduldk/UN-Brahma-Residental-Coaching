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
        //     $quota = \App\Models\OrganizationQuota::where('organization_id', $org->id)->first();
        //     if (!$quota) {
        //         \App\Models\OrganizationQuota::create([
        //             'organization_id' => $org->id,
        //             'available_forms' => 1,
        //             'used_forms' => \App\Models\ApplicationForm::where('organization_id', $org->id)->count(),
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
