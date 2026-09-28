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
        Schema::create('admission_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            $table->string('payment_type'); // full, emi, cash
            $table->decimal('total_amount', 10, 2); // Base course fee
            $table->decimal('discount_value', 10, 2)->nullable(); // Value of discount
            $table->string('discount_type')->nullable(); // percentage, fixed
            $table->decimal('final_amount', 10, 2); // Amount after discount
            $table->decimal('paid_amount', 10, 2); // Amount actually paid in this txn
            $table->integer('emi_months')->nullable(); // If EMI
            $table->string('payer_name')->nullable(); // If Cash
            $table->string('payer_phone')->nullable(); // If Cash
            $table->string('receiver_name')->nullable(); // Admin name
            $table->foreignId('received_by')->nullable()->constrained('users'); // Admin ID
            $table->string('transaction_id')->nullable(); // If online
            $table->string('status')->default('completed'); // completed, pending
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_payments');
    }
};
