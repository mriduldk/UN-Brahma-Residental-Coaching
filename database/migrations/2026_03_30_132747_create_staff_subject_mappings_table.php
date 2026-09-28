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
        Schema::create('staff_subject_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_staff_id')->constrained('school_staff')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('school_academic_years')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('school_sections')->cascadeOnDelete();
            $table->string('subject_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_subject_mappings');
    }
};
