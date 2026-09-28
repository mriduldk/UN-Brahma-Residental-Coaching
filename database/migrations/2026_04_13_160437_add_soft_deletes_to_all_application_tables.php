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
        $tables = [
            'users',
            'students',
            'addresses',
            'academic_qualifications',
            'student_parents',
            'bank_details',
            'student_documents',
            'application_forms',
            'student_applications',
            'organizations',
            'payment_orders',
            'payment_transactions',
            'institutions',
            'admission_payments',
            'admissions',
            'admission_students',
            'student_other_documents',
            'global_configs',
            'organization_quotas',
            'quota_payment_orders',
            'quota_payment_transactions',
            'organization_configurations',
            'form_links',
            'school_academic_years',
            'school_classes',
            'school_sections',
            'school_enrollments',
            'school_staff',
            'staff_subject_mappings',
            'student_attendances',
            'staff_attendances',
            'school_timetables',
            'school_fee_configs',
            'school_fee_payments',
            'notification_templates',
            'notifications',
            'notification_logs',
            'live_classes',
            'exams',
            'questions',
            'options',
            'exam_attempts',
            'student_answers',
            'question_banks',
            'setup_guides',
            'school_leaves',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'students',
            'addresses',
            'academic_qualifications',
            'student_parents',
            'bank_details',
            'student_documents',
            'application_forms',
            'student_applications',
            'organizations',
            'payment_orders',
            'payment_transactions',
            'institutions',
            'admission_payments',
            'admissions',
            'admission_students',
            'student_other_documents',
            'global_configs',
            'organization_quotas',
            'quota_payment_orders',
            'quota_payment_transactions',
            'organization_configurations',
            'form_links',
            'school_academic_years',
            'school_classes',
            'school_sections',
            'school_enrollments',
            'school_staff',
            'staff_subject_mappings',
            'student_attendances',
            'staff_attendances',
            'school_timetables',
            'school_fee_configs',
            'school_fee_payments',
            'notification_templates',
            'notifications',
            'notification_logs',
            'live_classes',
            'exams',
            'questions',
            'options',
            'exam_attempts',
            'student_answers',
            'question_banks',
            'setup_guides',
            'school_leaves',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
