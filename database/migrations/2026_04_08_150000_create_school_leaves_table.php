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
        Schema::create('school_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            
            // Polymorphic relation to Staff or Student Mapping
            $table->unsignedBigInteger('leavable_id');
            $table->string('leavable_type');
            
            $table->enum('leave_type', ['Sick', 'Casual', 'Emergency', 'Annual', 'Maternity', 'Paternity', 'Others'])->default('Casual');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->text('reason');
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            $table->index(['leavable_id', 'leavable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_leaves');
    }
};
