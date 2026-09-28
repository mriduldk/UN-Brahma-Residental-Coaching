<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organization_configurations', function (Blueprint $table) {
            $table->boolean('is_registration_fee_enabled')->default(false)->change();
            $table->boolean('is_admission_enabled')->default(false)->change();
        });

        // Update existing records to false
        \DB::table('organization_configurations')->update([
            'is_registration_fee_enabled' => false,
            'is_admission_enabled' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('false', function (Blueprint $table) {
            //
        });
    }
};
