<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE admission_students MODIFY COLUMN payment_type ENUM('full', 'emi', 'cash', 'dd_full', 'dd_emi') NOT NULL");
            DB::statement("ALTER TABLE admission_payments MODIFY COLUMN payment_type ENUM('full', 'emi', 'cash', 'dd_full', 'dd_emi') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this might cause data loss if there are 'dd_full' or 'dd_emi' rows.
        // For safety, we can just leave it or revert back to the original if empty.
        DB::statement("ALTER TABLE admission_students MODIFY COLUMN payment_type ENUM('full', 'emi', 'cash') NOT NULL");
        DB::statement("ALTER TABLE admission_payments MODIFY COLUMN payment_type ENUM('full', 'emi', 'cash') NOT NULL");
    }
};
