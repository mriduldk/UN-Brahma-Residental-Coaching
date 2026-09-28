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
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_application_id')->constrained('student_applications')->onDelete('cascade');
            $table->string('razorpay_order_id')->unique();
            $table->decimal('amount', 10, 2); // Amount in INR (e.g., 500.00)
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('amount_due', 10, 2)->default(0);
            $table->string('currency')->default('INR');
            $table->string('receipt')->nullable();
            $table->string('status')->default('created'); // created, attempted, paid
            $table->integer('attempts')->default(0);
            $table->json('notes')->nullable();
            $table->text('json_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_orders');
    }
};
