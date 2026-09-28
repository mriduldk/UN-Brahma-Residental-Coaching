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
        Schema::create('organization_lead_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('call_status')->nullable(); // pending, picked_up, not_picked, wrong_number
            $table->string('lead_temperature')->nullable(); // cold, warm, hot, converted
            $table->timestamp('next_followup_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('last_called_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_called_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_lead_trackings');
    }
};
