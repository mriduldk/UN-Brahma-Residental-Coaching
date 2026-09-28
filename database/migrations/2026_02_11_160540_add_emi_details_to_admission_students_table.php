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
        Schema::table('admission_students', function (Blueprint $table) {
            $table->integer('emi_months')->nullable()->after('payment_type');
            $table->decimal('emi_amount', 12, 2)->nullable()->after('emi_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_students', function (Blueprint $table) {
            $table->dropColumn(['emi_months', 'emi_amount']);
        });
    }
};
