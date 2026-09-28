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
        Schema::create('admission_payment_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            $table->foreignId('application_form_id')->constrained('application_forms')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('admission_payment_id')->nullable()->constrained('admission_payments')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('razorpay_order_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('currency')->default('INR');
            $table->string('status')->default('created');
            $table->integer('attempts')->default(0);
            $table->string('payment_category'); // full, emi
            $table->string('receipt')->nullable();
            $table->json('notes')->nullable();
            $table->text('json_response')->nullable();
            $table->timestamps();
        });

        Schema::create('admission_payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_payment_order_id')->constrained('admission_payment_orders', 'id', 'adm_pay_order_idx')->onDelete('cascade');
            $table->string('razorpay_payment_id')->unique();
            $table->string('razorpay_signature')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('INR');
            $table->string('status')->nullable();
            $table->string('method')->nullable();
            $table->string('bank')->nullable();
            $table->string('wallet')->nullable();
            $table->string('vpa')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_description')->nullable();
            $table->text('json_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_payment_transactions');
        Schema::dropIfExists('admission_payment_orders');
    }
};
