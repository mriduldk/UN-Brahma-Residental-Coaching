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
        Schema::table('global_configs', function (Blueprint $table) {
            $table->decimal('registration_fee_charge', 10, 2)->default(1000.00)->after('form_creation_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_configs', function (Blueprint $table) {
            $table->dropColumn('registration_fee_charge');
        });
    }
};
