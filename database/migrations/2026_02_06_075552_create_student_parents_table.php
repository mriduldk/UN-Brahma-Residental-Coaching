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
        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // Father Details
            $table->string('father_name');
            $table->string('father_mobile')->nullable();
            $table->string('father_email')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_qualification')->nullable();
            $table->decimal('father_annual_income', 10, 2)->nullable();

            // Mother Details
            $table->string('mother_name');
            $table->string('mother_mobile')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_qualification')->nullable();
            $table->decimal('mother_annual_income', 10, 2)->nullable();

            // Guardian Details (if applicable)
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('guardian_address')->nullable();
            $table->string('guardian_occupation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};
