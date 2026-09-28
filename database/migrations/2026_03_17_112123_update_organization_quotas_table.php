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
        Schema::table('organization_quotas', function (Blueprint $table) {
            $table->integer('available_forms')->default(1)->after('organization_id');
            $table->integer('used_forms')->default(0)->after('available_forms');
            $table->dropColumn('additional_forms_purchased');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_quotas', function (Blueprint $table) {
            //
        });
    }
};
