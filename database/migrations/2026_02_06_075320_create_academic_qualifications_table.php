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
        Schema::create('academic_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('examination'); // HSLC, HS, GRADUATION
            $table->string('board_university');
            $table->year('passing_year');
            $table->string('roll_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('percentage_grade');
            $table->string('medium_of_study')->nullable();
            $table->string('division');
            $table->decimal('total_marks', 8, 2)->nullable();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_qualifications');
    }
};
