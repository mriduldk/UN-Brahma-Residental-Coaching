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
        Schema::table('setup_guides', function (Blueprint $table) {
            $table->boolean('has_mapped_questions')->default(false)->after('has_exams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setup_guides', function (Blueprint $table) {
            $table->dropColumn('has_mapped_questions');
        });
    }
};
