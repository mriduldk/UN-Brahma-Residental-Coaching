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
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('school_academic_years')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('school_sections')->onDelete('set null');
            $table->foreignId('staff_id')->constrained('school_staff')->onDelete('cascade');
            
            $table->string('subject_name');
            $table->string('topic');
            $table->string('meeting_id')->nullable();
            $table->text('start_url')->nullable();
            $table->text('join_url');
            $table->string('password')->nullable();
            
            $table->dateTime('start_time');
            $table->integer('duration')->default(40); // in minutes
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_classes');
    }
};
