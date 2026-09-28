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
        Schema::create('admission_students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_code')->unique();
            $table->string('application_code');
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            $table->foreignId('application_form_id')->constrained('application_forms')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->string('academic_session');
            
            $table->enum('payment_type', ['full', 'emi', 'cash']);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            
            $table->decimal('total_payable_amount', 10, 2);
            $table->decimal('total_paid_amount', 10, 2)->default(0);
            $table->decimal('pending_amount', 10, 2)->default(0);
            
            $table->date('next_payment_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_students');
    }
};
