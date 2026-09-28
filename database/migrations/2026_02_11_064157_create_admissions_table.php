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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('academic_session');
            
            // Fee Structure
            $table->decimal('admission_fee', 10, 2);
            
            // Full Payment Config
            $table->boolean('allow_full_payment')->default(true);
            $table->decimal('full_payment_discount_amount', 10, 2)->nullable()->default(0);
            $table->decimal('full_payment_discount_percentage', 5, 2)->nullable()->default(0);
            
            // EMI Config
            $table->boolean('allow_emi')->default(false);
            $table->json('emi_tenures')->nullable(); // e.g., [3, 6, 9]
            $table->decimal('emi_down_payment', 10, 2)->nullable();
            
            // Cash Config
            $table->boolean('allow_cash_payment')->default(false);
            $table->integer('cash_payment_days')->nullable();
            
            // Metadata
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
