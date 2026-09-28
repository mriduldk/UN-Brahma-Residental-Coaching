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
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('question_bank_id')->constrained('school_academic_years')->onDelete('cascade');
            $table->foreignId('school_class_id')->nullable()->after('academic_year_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('school_section_id')->nullable()->after('school_class_id')->constrained('school_sections')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['school_class_id']);
            $table->dropForeign(['school_section_id']);
            $table->dropColumn(['academic_year_id', 'school_class_id', 'school_section_id']);
        });
    }
};
