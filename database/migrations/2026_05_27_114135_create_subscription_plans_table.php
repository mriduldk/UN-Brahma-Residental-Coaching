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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('business_type')->unique(); // e.g. Tuition, School, Coaching
            $table->decimal('standard_price', 10, 2);
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->integer('free_months')->default(3);
            $table->integer('discounted_months')->default(3); // e.g. next 3 months
            $table->timestamps();
        });
        
        // Seed default plans
        \Illuminate\Support\Facades\DB::table('subscription_plans')->insert([
            [
                'business_type' => 'Tuition',
                'standard_price' => 499.00,
                'discounted_price' => 299.00,
                'free_months' => 3,
                'discounted_months' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type' => 'School',
                'standard_price' => 899.00,
                'discounted_price' => 599.00,
                'free_months' => 3,
                'discounted_months' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type' => 'Coaching',
                'standard_price' => 999.00,
                'discounted_price' => 699.00,
                'free_months' => 3,
                'discounted_months' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
