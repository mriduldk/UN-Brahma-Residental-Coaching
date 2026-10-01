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
        if (!Schema::hasTable('btcla_constituencies')) {
            Schema::create('btcla_constituencies', function (Blueprint $table) {
                $table->id();
                $table->integer('number')->nullable();
                $table->string('name')->unique();
                $table->string('category')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });

            $constituencies = [
                ['number' => 1, 'name' => 'Parbatjhora (ST) Constituency', 'category' => 'ST', 'order' => 1],
                ['number' => 2, 'name' => 'Guma (Open) Constituency', 'category' => 'Open', 'order' => 2],
                ['number' => 3, 'name' => 'Srirampur (Non-ST) Constituency', 'category' => 'Non-ST', 'order' => 3],
                ['number' => 4, 'name' => 'Jamduar (ST) Constituency', 'category' => 'ST', 'order' => 4],
                ['number' => 5, 'name' => 'Soraibil (ST) Constituency', 'category' => 'ST', 'order' => 5],
                ['number' => 6, 'name' => 'Kachugaon (ST) Constituency', 'category' => 'ST', 'order' => 6],
                ['number' => 7, 'name' => 'Fakiragram (Non-ST) Constituency', 'category' => 'Non-ST', 'order' => 7],
                ['number' => 8, 'name' => 'Dotma (ST) Constituency', 'category' => 'ST', 'order' => 8],
                ['number' => 9, 'name' => 'Banargaon (ST) Constituency', 'category' => 'ST', 'order' => 9],
                ['number' => 10, 'name' => 'Debargaon (ST) Constituency', 'category' => 'ST', 'order' => 10],
                ['number' => 11, 'name' => 'Baukhungri (ST) Constituency', 'category' => 'ST', 'order' => 11],
                ['number' => 12, 'name' => 'Salakati (ST) Constituency', 'category' => 'ST', 'order' => 12],
                ['number' => 13, 'name' => 'Chirang (ST) Constituency', 'category' => 'ST', 'order' => 13],
                ['number' => 14, 'name' => 'Chirang Duars (ST) Constituency', 'category' => 'ST', 'order' => 14],
                ['number' => 15, 'name' => 'Kajalgaon (ST) Constituency', 'category' => 'ST', 'order' => 15],
                ['number' => 16, 'name' => 'Nichima (ST) Constituency', 'category' => 'ST', 'order' => 16],
                ['number' => 17, 'name' => 'Sobaijhar (ST) Constituency', 'category' => 'ST', 'order' => 17],
                ['number' => 18, 'name' => 'Manas Serfang (ST) Constituency', 'category' => 'ST', 'order' => 18],
                ['number' => 19, 'name' => 'Thuribari (Open) Constituency', 'category' => 'Open', 'order' => 19],
                ['number' => 20, 'name' => 'Mathanguri (Open) Constituency', 'category' => 'Open', 'order' => 20],
                ['number' => 21, 'name' => 'Salbari (ST) Constituency', 'category' => 'ST', 'order' => 21],
                ['number' => 22, 'name' => 'Koklabari (ST) Constituency', 'category' => 'ST', 'order' => 22],
                ['number' => 23, 'name' => 'Dihira (Open) Constituency', 'category' => 'Open', 'order' => 23],
                ['number' => 24, 'name' => 'Mushalpur (ST) Constituency', 'category' => 'ST', 'order' => 24],
                ['number' => 25, 'name' => 'Baganpara (ST) Constituency', 'category' => 'ST', 'order' => 25],
                ['number' => 26, 'name' => 'Darrangajuli (ST) Constituency', 'category' => 'ST', 'order' => 26],
                ['number' => 27, 'name' => 'Nagrijuli (Non-ST) Constituency', 'category' => 'Non-ST', 'order' => 27],
                ['number' => 28, 'name' => 'Goibari (ST) Constituency', 'category' => 'ST', 'order' => 28],
                ['number' => 29, 'name' => 'Suklai Serfang (ST) Constituency', 'category' => 'ST', 'order' => 29],
                ['number' => 30, 'name' => 'Goreswar (ST) Constituency', 'category' => 'ST', 'order' => 30],
                ['number' => 31, 'name' => 'Khwirwbari (ST) Constituency', 'category' => 'ST', 'order' => 31],
                ['number' => 32, 'name' => 'Bhergaon (ST) Constituency', 'category' => 'ST', 'order' => 32],
                ['number' => 33, 'name' => 'Nonwi Serfang (Non-ST) Constituency', 'category' => 'Non-ST', 'order' => 33],
                ['number' => 34, 'name' => 'Khaling Duar (ST) Constituency', 'category' => 'ST', 'order' => 34],
                ['number' => 35, 'name' => 'Madwibari (Open) Constituency', 'category' => 'Open', 'order' => 35],
                ['number' => 36, 'name' => 'Horisinga (ST) Constituency', 'category' => 'ST', 'order' => 36],
                ['number' => 37, 'name' => 'Dwhwnsri (ST) Constituency', 'category' => 'ST', 'order' => 37],
                ['number' => 38, 'name' => 'Bhairabkunda (ST) Constituency', 'category' => 'ST', 'order' => 38],
                ['number' => 39, 'name' => 'Pasnwi Serfang (Non-ST) Constituency', 'category' => 'Non-ST', 'order' => 39],
                ['number' => 40, 'name' => 'Rowta (ST) Constituency', 'category' => 'ST', 'order' => 40],
            ];

            $now = now();
            foreach ($constituencies as $c) {
                DB::table('btcla_constituencies')->insert(array_merge($c, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('btcla_constituencies');
    }
};
