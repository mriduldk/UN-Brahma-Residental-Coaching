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
        // 1. Create communities table
        if (!Schema::hasTable('communities')) {
            Schema::create('communities', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('code')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });

            // Seed initial standard communities
            $communities = [
                ['name' => 'Bodo', 'code' => 'BODO', 'order' => 1],
                ['name' => 'Assamese', 'code' => 'ASM', 'order' => 2],
                ['name' => 'Bengali', 'code' => 'BEN', 'order' => 3],
                ['name' => 'Koch-Rajbongshi', 'code' => 'KR', 'order' => 4],
                ['name' => 'Rabha', 'code' => 'RABHA', 'order' => 5],
                ['name' => 'Garo', 'code' => 'GARO', 'order' => 6],
                ['name' => 'Dimasa', 'code' => 'DIMASA', 'order' => 7],
                ['name' => 'Karbi', 'code' => 'KARBI', 'order' => 8],
                ['name' => 'Mishing', 'code' => 'MISHING', 'order' => 9],
                ['name' => 'Tiwa', 'code' => 'TIWA', 'order' => 10],
                ['name' => 'Adivasi / Tea Tribe', 'code' => 'ADIVASI', 'order' => 11],
                ['name' => 'Nepali / Gorkha', 'code' => 'NEPALI', 'order' => 12],
                ['name' => 'Manipuri / Meitei', 'code' => 'MANIPURI', 'order' => 13],
                ['name' => 'Santhal', 'code' => 'SANTHAL', 'order' => 14],
                ['name' => 'Hindi Speaking', 'code' => 'HINDI', 'order' => 15],
                ['name' => 'Muslim', 'code' => 'MUSLIM', 'order' => 16],
                ['name' => 'General / Other', 'code' => 'OTHER', 'order' => 99],
            ];

            $now = now();
            foreach ($communities as $c) {
                DB::table('communities')->insert(array_merge($c, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        // 2. Rename nationality column in students table
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'nationality') && !Schema::hasColumn('students', 'community')) {
                $table->renameColumn('nationality', 'community');
            } elseif (!Schema::hasColumn('students', 'community')) {
                $table->string('community')->nullable()->after('religion');
            }
        });

        // 3. Rename nationality column in application_students table
        Schema::table('application_students', function (Blueprint $table) {
            if (Schema::hasColumn('application_students', 'nationality') && !Schema::hasColumn('application_students', 'community')) {
                $table->renameColumn('nationality', 'community');
            } elseif (!Schema::hasColumn('application_students', 'community')) {
                $table->string('community')->nullable()->after('religion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_students', function (Blueprint $table) {
            if (Schema::hasColumn('application_students', 'community') && !Schema::hasColumn('application_students', 'nationality')) {
                $table->renameColumn('community', 'nationality');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'community') && !Schema::hasColumn('students', 'nationality')) {
                $table->renameColumn('community', 'nationality');
            }
        });

        Schema::dropIfExists('communities');
    }
};
