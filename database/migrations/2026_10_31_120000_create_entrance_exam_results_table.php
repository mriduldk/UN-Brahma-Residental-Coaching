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
        if (!Schema::hasTable('entrance_exam_results')) {
            Schema::create('entrance_exam_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_application_id')->nullable()->constrained('student_applications')->onDelete('set null');
                $table->foreignId('application_form_id')->nullable()->constrained('application_forms')->onDelete('set null');
                $table->string('stream', 30)->index(); // APSC, UPSC, NEET, JEE
                $table->string('roll_no', 30)->unique(); // UNIQUE(roll_no) as specified
                $table->string('candidate_name', 150);
                $table->string('phone_number', 25)->nullable()->index();
                $table->string('district', 60)->nullable()->index();
                $table->decimal('marks', 6, 2)->index();
                $table->unsignedInteger('rank')->nullable()->index();
                $table->unsignedInteger('district_rank')->nullable();
                $table->enum('qualification_status', ['qualified', 'waitlisted', 'not_qualified', 'absent'])->default('not_qualified');
                $table->string('selection_quota', 60)->nullable();
                $table->string('remarks', 255)->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamp('published_at')->nullable();
                $table->json('raw_payload')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrance_exam_results');
    }
};
