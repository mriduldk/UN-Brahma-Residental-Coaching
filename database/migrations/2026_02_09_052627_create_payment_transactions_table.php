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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_order_id')->constrained('payment_orders')->onDelete('cascade');
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            $table->string('razorpay_payment_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('INR');
            $table->string('status'); // created, authorized, captured, refunded, failed
            $table->string('method')->nullable(); // card, netbanking, wallet, upi
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
        Schema::dropIfExists('payment_transactions');
    }
};
