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
        Schema::table('quota_payment_orders', function (Blueprint $table) {
            $table->foreignId('application_form_id')->nullable()->constrained('application_forms')->onDelete('set null')->after('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quota_payment_orders', function (Blueprint $table) {
            $table->dropForeign(['application_form_id']);
            $table->dropColumn('application_form_id');
        });
    }
};
