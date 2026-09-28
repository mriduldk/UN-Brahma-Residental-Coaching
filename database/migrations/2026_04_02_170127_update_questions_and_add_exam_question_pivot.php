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
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        Schema::create('exam_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->nullable()->after('id')->constrained()->onDelete('set null');
        });

        // Data Migration
        $exams = \DB::table('exams')->get();
        if ($exams->count() > 0) {
            $bankId = \DB::table('question_banks')->insertGetId([
                'title' => 'Default Bank',
                'description' => 'Automatically created during Question Bank migration',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $questions = \DB::table('questions')->get();
            foreach ($questions as $q) {
                \DB::table('questions')->where('id', $q->id)->update(['question_bank_id' => $bankId]);
                
                if ($q->exam_id) {
                    \DB::table('exam_question')->insert([
                        'exam_id' => $q->exam_id,
                        'question_id' => $q->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            //
        });
    }
};
