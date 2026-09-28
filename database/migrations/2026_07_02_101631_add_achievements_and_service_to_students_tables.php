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
            $table->boolean('is_ex_serviceman')->nullable();
            $table->string('ncc_certificate')->nullable();
            $table->boolean('has_national_sports_achievement')->nullable();
            $table->boolean('has_fine_arts_cultural_achievement')->nullable();
        });

        Schema::table('application_students', function (Blueprint $table) {
            $table->boolean('is_ex_serviceman')->nullable();
            $table->string('ncc_certificate')->nullable();
            $table->boolean('has_national_sports_achievement')->nullable();
            $table->boolean('has_fine_arts_cultural_achievement')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'is_ex_serviceman',
                'ncc_certificate',
                'has_national_sports_achievement',
                'has_fine_arts_cultural_achievement'
            ]);
        });

        Schema::table('application_students', function (Blueprint $table) {
            $table->dropColumn([
                'is_ex_serviceman',
                'ncc_certificate',
                'has_national_sports_achievement',
                'has_fine_arts_cultural_achievement'
            ]);
        });
    }
};
