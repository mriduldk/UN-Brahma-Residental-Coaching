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
        Schema::create('global_configs', function (Blueprint $table) {
            $table->id();
            $table->decimal('form_creation_charge', 10, 2)->default(500.00);
            $table->integer('free_form_limit')->default(1);
            $table->timestamps();
        });

        // Insert default config
        DB::table('global_configs')->insert([
            'form_creation_charge' => 500.00,
            'free_form_limit' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_configs');
    }
};
