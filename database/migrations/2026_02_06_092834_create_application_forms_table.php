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
        Schema::create('application_forms', function (Blueprint $table) {
            $table->id();
            $table->string('application_code')->unique()->comment('System generated ID e.g. ITI-2025-001');
            $table->string('application_name');
            $table->text('application_description')->nullable();
            $table->string('session'); // e.g. 2025-2026
            $table->decimal('application_fee', 8, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->date('deadline')->nullable();
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // JSON column to store the visibility configuration of fields
            // Structure: { "personal": ["full_name", "dob"], "address": ["present_address"], ... }
            $table->json('field_config')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_forms');
    }
};
