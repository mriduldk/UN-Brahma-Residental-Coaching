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
        Schema::table('student_documents', function (Blueprint $table) {
            $table->string('bed_parangat_marksheet')->nullable();
            $table->string('bed_parangat_certificate')->nullable();
            $table->string('tet_certificate')->nullable();
            $table->string('pwd_certificate')->nullable();
            $table->string('ex_serviceman_certificate')->nullable();
            $table->string('ncc_certificate_doc')->nullable();
            $table->string('sports_certificate')->nullable();
            $table->string('cultural_certificate')->nullable();
            $table->string('btc_residential_certificate')->nullable();
            $table->string('language_diploma_certificate')->nullable();
        });

        Schema::table('application_documents', function (Blueprint $table) {
            $table->string('bed_parangat_marksheet')->nullable();
            $table->string('bed_parangat_certificate')->nullable();
            $table->string('tet_certificate')->nullable();
            $table->string('pwd_certificate')->nullable();
            $table->string('ex_serviceman_certificate')->nullable();
            $table->string('ncc_certificate_doc')->nullable();
            $table->string('sports_certificate')->nullable();
            $table->string('cultural_certificate')->nullable();
            $table->string('btc_residential_certificate')->nullable();
            $table->string('language_diploma_certificate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents_tables', function (Blueprint $table) {
            //
        });
    }
};
