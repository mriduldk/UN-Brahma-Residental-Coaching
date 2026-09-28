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
            DB::statement("ALTER TABLE student_attendances MODIFY COLUMN status ENUM('Present', 'Absent', 'Half Day', 'Late', 'Leave', 'Holiday') NOT NULL");
            DB::statement("ALTER TABLE staff_attendances MODIFY COLUMN status ENUM('Present', 'Absent', 'Half Day', 'Late', 'Leave', 'Holiday') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_status', function (Blueprint $table) {
            //
        });
    }
};
