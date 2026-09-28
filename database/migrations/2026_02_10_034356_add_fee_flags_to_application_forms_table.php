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
        Schema::table('application_forms', function (Blueprint $table) {
            $table->boolean('is_application_fee_required')->default(true);
            $table->boolean('is_admission_required')->default(true);
            $table->decimal('admission_fee', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_forms', function (Blueprint $table) {
            $table->dropColumn(['is_application_fee_required', 'is_admission_required', 'admission_fee']);
        });
    }
};
