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
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_staff_id')->constrained('school_staff')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['Present', 'Absent', 'Half Day', 'Late', 'Leave']);
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['school_staff_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
