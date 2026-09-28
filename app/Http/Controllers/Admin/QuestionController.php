<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;
use App\Http\Requests\StoreQuestionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function store(StoreQuestionRequest $request, Exam $exam)
    {
        DB::transaction(function () use ($request, $exam) {
            $question = $exam->questions()->create([
                'question_text' => $request->question_text,
                'marks' => $request->marks,
                'is_previous_year' => $request->filled('previous_year'),
                'previous_year' => $request->previous_year
            ]);

            foreach ($request->options as $index => $optionData) {
                $question->options()->create([
                    'option_text' => $optionData['text'],
                    'is_correct' => $index == $request->correct_option_index
                ]);
            }
        });

        return redirect()->back()->with('success', 'Question added successfully.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->back()->with('success', 'Question deleted successfully.');
    }
}
