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
        // 1. Add applied_for_options to application_forms
        if (Schema::hasTable('application_forms') && !Schema::hasColumn('application_forms', 'applied_for_options')) {
            Schema::table('application_forms', function (Blueprint $table) {
                $table->text('applied_for_options')->nullable()->after('application_description');
            });
        }

        // 2. Add applied_for to student_applications
        if (Schema::hasTable('student_applications') && !Schema::hasColumn('student_applications', 'applied_for')) {
            Schema::table('student_applications', function (Blueprint $table) {
                $table->string('applied_for')->nullable()->after('application_form_id');
            });
        }

        // 3. Add applied_for to application_students
        if (Schema::hasTable('application_students') && !Schema::hasColumn('application_students', 'applied_for')) {
            Schema::table('application_students', function (Blueprint $table) {
                $table->string('applied_for')->nullable()->after('full_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('application_students') && Schema::hasColumn('application_students', 'applied_for')) {
            Schema::table('application_students', function (Blueprint $table) {
                $table->dropColumn('applied_for');
            });
        }

        if (Schema::hasTable('student_applications') && Schema::hasColumn('student_applications', 'applied_for')) {
            Schema::table('student_applications', function (Blueprint $table) {
                $table->dropColumn('applied_for');
            });
        }

        if (Schema::hasTable('application_forms') && Schema::hasColumn('application_forms', 'applied_for_options')) {
            Schema::table('application_forms', function (Blueprint $table) {
                $table->dropColumn('applied_for_options');
            });
        }
    }
};
