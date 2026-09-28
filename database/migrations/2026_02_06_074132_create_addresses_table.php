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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // Permanent Address
            $table->string('permanent_village_town');
            $table->string('permanent_street')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_district');
            $table->string('permanent_state');
            $table->string('permanent_country');
            $table->string('permanent_pin_code');

            // Present Address
            $table->boolean('same_as_permanent')->default(false);
            $table->string('present_village_town')->nullable();
            $table->string('present_street')->nullable();
            $table->string('present_city')->nullable();
            $table->string('present_district')->nullable();
            $table->string('present_state')->nullable();
            $table->string('present_country')->nullable();
            $table->string('present_pin_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
