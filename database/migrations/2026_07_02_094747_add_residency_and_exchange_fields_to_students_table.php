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
        Schema::table('students', function (Blueprint $table) {
            $table->string('employment_exchange_no')->nullable()->after('identification_mark');
            $table->boolean('is_permanent_resident_of_assam')->nullable()->after('employment_exchange_no');
            $table->boolean('is_btc_resident')->nullable()->after('is_permanent_resident_of_assam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'employment_exchange_no',
                'is_permanent_resident_of_assam',
                'is_btc_resident'
            ]);
        });
    }
};
