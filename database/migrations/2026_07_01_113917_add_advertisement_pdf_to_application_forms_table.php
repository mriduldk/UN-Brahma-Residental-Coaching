<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_forms', function (Blueprint $table) {
            $table->string('advertisement_pdf')->nullable()->after('deadline')->comment('Path to the uploaded advertisement PDF file');
        });
    }

    public function down(): void
    {
        Schema::table('application_forms', function (Blueprint $table) {
            $table->dropColumn('advertisement_pdf');
        });
    }
};
