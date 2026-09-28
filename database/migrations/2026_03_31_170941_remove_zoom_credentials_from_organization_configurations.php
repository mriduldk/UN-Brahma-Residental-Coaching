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
            $table->dropColumn(['zoom_account_id', 'zoom_client_id', 'zoom_client_secret']);
        });
    }

    public function down(): void
    {
        Schema::table('organization_configurations', function (Blueprint $table) {
            $table->string('zoom_account_id')->nullable();
            $table->string('zoom_client_id')->nullable();
            $table->string('zoom_client_secret')->nullable();
        });
    }
};
