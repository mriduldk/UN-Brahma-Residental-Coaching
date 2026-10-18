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
        if (Schema::hasTable('student_applications') && !Schema::hasColumn('student_applications', 'roll_no')) {
            Schema::table('student_applications', function (Blueprint $table) {
                $table->string('roll_no', 30)->nullable()->index()->after('application_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_applications') && Schema::hasColumn('student_applications', 'roll_no')) {
            Schema::table('student_applications', function (Blueprint $table) {
                $table->dropColumn('roll_no');
            });
        }
    }
};
