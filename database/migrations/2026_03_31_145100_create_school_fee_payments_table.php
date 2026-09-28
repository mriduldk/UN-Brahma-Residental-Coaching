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
        Schema::create('school_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('school_academic_years')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('school_enrollments')->onDelete('cascade');
            
            $table->integer('month'); // 1-12
            $table->integer('year');
            $table->decimal('amount_paid', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('cash');
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['paid', 'partial', 'pending'])->default('paid');
            $table->text('remarks')->nullable();
            
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id', 'month', 'year'], 'unique_monthly_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_fee_payments');
    }
};
