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
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // Personal
            $table->string('photo')->nullable();
            $table->string('signature')->nullable();
            $table->string('aadhaar_card')->nullable();
            $table->string('passport')->nullable();

            // Marksheets
            $table->string('hslc_marksheet')->nullable();
            $table->string('hs_marksheet')->nullable();
            $table->string('graduation_marksheet')->nullable();
            $table->string('post_graduation_marksheet')->nullable();

            // Pass Certificates
            $table->string('hslc_pass_certificate')->nullable();
            $table->string('hs_pass_certificate')->nullable();
            $table->string('graduation_pass_certificate')->nullable();
            $table->string('post_graduation_pass_certificate')->nullable();

            // Admit Cards
            $table->string('hslc_admit_card')->nullable();
            $table->string('hs_admit_card')->nullable();
            $table->string('graduation_admit_card')->nullable();
            $table->string('post_graduation_admit_card')->nullable();

            // Other Certificates
            $table->string('transfer_certificate')->nullable();
            $table->string('caste_certificate')->nullable();
            $table->string('income_certificate')->nullable();
            $table->string('address_proof')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
