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
            $table->string('zoom_account_id')->nullable()->after('is_admission_enabled');
            $table->string('zoom_client_id')->nullable()->after('zoom_account_id');
            $table->string('zoom_client_secret')->nullable()->after('zoom_client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_configurations', function (Blueprint $table) {
            $table->dropColumn(['zoom_account_id', 'zoom_client_id', 'zoom_client_secret']);
        });
    }
};
