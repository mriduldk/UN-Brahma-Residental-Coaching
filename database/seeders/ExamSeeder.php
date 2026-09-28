<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;
use Carbon\Carbon;
use App\Models\User;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        $exam = Exam::create([
            'title' => 'General Science Quiz 2026',
            'description' => 'A comprehensive test covering basic physics, chemistry and biology.',
            'duration_minutes' => 30,
            'start_time' => Carbon::now()->subDay(),
            'end_time' => Carbon::now()->addDays(7),
            'negative_marks_per_wrong' => 0.25,
            'total_marks' => 20,
            'passing_marks' => 8,
            'created_by' => $admin ? $admin->id : null,
            'max_attempts' => 2,
        ]);

        $q1 = $exam->questions()->create([
            'question_text' => 'What is the chemical symbol for Gold?',
            'marks' => 5,
        ]);
        $q1->options()->createMany([
            ['option_text' => 'Au', 'is_correct' => true],
            ['option_text' => 'Ag', 'is_correct' => false],
            ['option_text' => 'Fe', 'is_correct' => false],
            ['option_text' => 'Cu', 'is_correct' => false],
        ]);

        $q2 = $exam->questions()->create([
            'question_text' => 'Which planet is known as the Red Planet?',
            'marks' => 5,
            'is_previous_year' => true,
            'previous_year' => '2023',
        ]);
        $q2->options()->createMany([
            ['option_text' => 'Mars', 'is_correct' => true],
            ['option_text' => 'Jupiter', 'is_correct' => false],
            ['option_text' => 'Venus', 'is_correct' => false],
            ['option_text' => 'Saturn', 'is_correct' => false],
        ]);

        $q3 = $exam->questions()->create([
            'question_text' => 'What is the power house of the cell?',
            'marks' => 10,
        ]);
        $q3->options()->createMany([
            ['option_text' => 'Mitochondria', 'is_correct' => true],
            ['option_text' => 'Nucleus', 'is_correct' => false],
            ['option_text' => 'Ribosome', 'is_correct' => false],
            ['option_text' => 'Golgi Apparatus', 'is_correct' => false],
        ]);
    }
}
