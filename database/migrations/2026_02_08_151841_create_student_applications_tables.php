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
        // 1. Student Applications Table (Main Table)
        if (!Schema::hasTable('student_applications')) {
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_code')->nullable(); // Unique App ID like APP-2025-001
            $table->foreignId('application_form_id')->constrained('application_forms')->onDelete('restrict');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['draft', 'pending', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
        }

        // 2. Application Students (Snapshot of Student Profile)
        if (!Schema::hasTable('application_students')) {
        Schema::create('application_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            $table->string('full_name');
            $table->string('phone_number'); // Keep as string, not unique in snapshot
            $table->string('email');        // Keep as string
            $table->date('dob')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('category')->nullable();
            $table->string('caste')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('identification_mark')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('alternate_mobile_number')->nullable();
            $table->timestamps();
        });
        }

        // 3. Application Addresses (Snapshot of Address)
        if (!Schema::hasTable('application_addresses')) {
        Schema::create('application_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            
            // Permanent Address
            $table->string('permanent_village_town')->nullable();
            $table->string('permanent_street')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_district')->nullable(); // Made nullable to match loose requirements
            $table->string('permanent_state')->nullable();
            $table->string('permanent_country')->nullable();
            $table->string('permanent_pin_code')->nullable();
            $table->boolean('same_as_permanent')->default(false);

            // Present Address
            $table->string('present_village_town')->nullable();
            $table->string('present_street')->nullable();
            $table->string('present_city')->nullable();
            $table->string('present_district')->nullable();
            $table->string('present_state')->nullable();
            $table->string('present_country')->nullable();
            $table->string('present_pin_code')->nullable();
            $table->timestamps();
        });
        }

        // 4. Application Bank Details (Snapshot)
        if (!Schema::hasTable('application_bank_details')) {
        Schema::create('application_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('upi_id')->nullable();
            $table->timestamps();
        });
        }

        // 5. Application Parent Details (Snapshot)
        if (!Schema::hasTable('application_parents')) {
        Schema::create('application_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            
            // Father
            $table->string('father_name')->nullable();
            $table->string('father_mobile')->nullable();
            $table->string('father_email')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_qualification')->nullable();
            $table->string('father_annual_income')->nullable(); // Kept as string for snapshot flexibility

            // Mother
            $table->string('mother_name')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_qualification')->nullable();
            $table->string('mother_annual_income')->nullable();

            // Guardian
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('guardian_address')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->timestamps();
        });
        }

        // 6. Application Academic Qualifications (Snapshot - Multiple Rows)
        if (!Schema::hasTable('application_academic_qualifications')) {
        Schema::create('application_academic_qualifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_application_id');
            $table->foreign('student_application_id', 'app_acad_qual_sa_fk')->references('id')->on('student_applications')->onDelete('cascade');
            $table->string('examination')->nullable();
            $table->string('board_university')->nullable();
            $table->string('passing_year')->nullable();
            $table->string('roll_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('percentage_grade')->nullable();
            $table->string('medium_of_study')->nullable();
            $table->string('division')->nullable();
            $table->string('total_marks')->nullable();
            $table->string('marks_obtained')->nullable();
            $table->timestamps();
        });
        }

         // 7. Application Documents (Snapshot)
         if (!Schema::hasTable('application_documents')) {
         Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            $table->string('document_type'); // e.g. 'photo', 'signature', 'caste_certificate'
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('application_academic_qualifications');
        Schema::dropIfExists('application_parents');
        Schema::dropIfExists('application_bank_details');
        Schema::dropIfExists('application_addresses');
        Schema::dropIfExists('application_students');
        Schema::dropIfExists('student_applications');
    }
};
