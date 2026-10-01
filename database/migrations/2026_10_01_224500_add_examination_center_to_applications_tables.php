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
        // 1. Add examination_center to student_applications
        if (Schema::hasTable('student_applications') && !Schema::hasColumn('student_applications', 'examination_center')) {
            Schema::table('student_applications', function (Blueprint $table) {
                $table->string('examination_center')->nullable()->after('applied_for');
            });
        }

        // 2. Add examination_center to application_students
        if (Schema::hasTable('application_students') && !Schema::hasColumn('application_students', 'examination_center')) {
            Schema::table('application_students', function (Blueprint $table) {
                $table->string('examination_center')->nullable()->after('applied_for');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('application_students') && Schema::hasColumn('application_students', 'examination_center')) {
            Schema::table('application_students', function (Blueprint $table) {
                $table->dropColumn('examination_center');
            });
        }

        if (Schema::hasTable('student_applications') && Schema::hasColumn('student_applications', 'examination_center')) {
            Schema::table('student_applications', function (Blueprint $table) {
                $table->dropColumn('examination_center');
            });
        }
    }
};
