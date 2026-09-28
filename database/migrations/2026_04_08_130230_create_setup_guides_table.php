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
        Schema::create('setup_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->boolean('has_academic_session')->default(false);
            $table->boolean('has_classes')->default(false);
            $table->boolean('has_students')->default(false);
            $table->boolean('has_question_bank')->default(false);
            $table->boolean('has_questions')->default(false);
            $table->boolean('has_exams')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setup_guides');
    }
};
