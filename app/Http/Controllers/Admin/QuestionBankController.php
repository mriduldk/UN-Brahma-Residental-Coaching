<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    public function index()
    {
        $orgId = Auth::user()->currentOrganization()->id;
        $banks = QuestionBank::where('organization_id', $orgId)
            ->withCount('questions')
            ->latest()
            ->paginate(10);
        return view('school.question-banks.index', compact('banks'));
    }

    public function create()
    {
        return view('school.question-banks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $orgId = Auth::user()->currentOrganization()->id;
        QuestionBank::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'organization_id' => $orgId
        ]);

        app(\App\Services\SetupGuideService::class)->updateStep($orgId, 'has_question_bank', true);

        return redirect()->route('administration.question-banks.index')->with('success', 'Question Bank created successfully.');
    }

    public function show(QuestionBank $questionBank)
    {
        $this->authorizeOrganization($questionBank);
        $questionBank->load('questions.options');
        return view('school.question-banks.show', compact('questionBank'));
    }

    public function edit(QuestionBank $questionBank)
    {
        $this->authorizeOrganization($questionBank);
        return view('school.question-banks.edit', compact('questionBank'));
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $this->authorizeOrganization($questionBank);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $questionBank->update($request->only('title', 'description'));

        return redirect()->route('administration.question-banks.index')->with('success', 'Question Bank updated successfully.');
    }

    public function destroy(QuestionBank $questionBank)
    {
        $this->authorizeOrganization($questionBank);
        $questionBank->delete();
        return redirect()->route('administration.question-banks.index')->with('success', 'Question Bank deleted successfully.');
    }

    public function getQuestions(QuestionBank $questionBank)
    {
        $this->authorizeOrganization($questionBank);
        return response()->json($questionBank->questions()->with('options')->get());
    }

    public function storeQuestion(Request $request, QuestionBank $questionBank)
    {
        $this->authorizeOrganization($questionBank);
        $request->validate([
            'question_text' => 'required|string',
            'marks' => 'required|numeric|min:0',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option_index' => 'required|integer'
        ]);

        DB::transaction(function () use ($request, $questionBank) {
            $question = $questionBank->questions()->create([
                'question_text' => $request->question_text,
                'marks' => $request->marks,
                'is_previous_year' => $request->is_previous_year ?? false,
                'previous_year' => $request->previous_year
            ]);

            foreach ($request->options as $index => $optionData) {
                $question->options()->create([
                    'option_text' => $optionData['text'],
                    'is_correct' => $index == $request->correct_option_index
                ]);
            }
        });

        app(\App\Services\SetupGuideService::class)->updateStep($questionBank->organization_id, 'has_questions', true);

        return redirect()->back()->with('success', 'Question added to bank successfully.');
    }

    public function importQuestions(Request $request, QuestionBank $questionBank)
    {
        $this->authorizeOrganization($questionBank);
        $request->validate([
            'csv_file' => 'required|file|max:2048',
        ]);

        $file = $request->file('csv_file');
        
        // Ensure it's a readable file
        $content = file_get_contents($file->getRealPath());
        
        // Handle BOM (Byte Order Mark) for UTF-8
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        // Convert to UTF-8 if it's not (robust attempt)
        if (!mb_check_encoding($content, 'UTF-8')) {
            $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'UTF-16'];
            $guessed = mb_detect_encoding($content, $encodings, true);
            
            if ($guessed && $guessed !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $guessed);
            }
        }

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);
        
        // Detect delimiter (rare but good for Excel ; vs ,)
        $firstLine = fgets($stream);
        $delimiter = (strpos($firstLine, ';') !== false && strpos($firstLine, ',') === false) ? ';' : ',';
        rewind($stream);

        // Skip header
        fgetcsv($stream, 0, $delimiter);

        $importCount = 0;
        $rowNumber = 1;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($stream, 0, $delimiter)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) continue;

                // Basic validation
                if (count($row) < 7) {
                    $errors[] = "Row $rowNumber: Insufficient columns (found " . count($row) . ").";
                    continue;
                }

                if (empty($row[0])) {
                    $errors[] = "Row $rowNumber: Question text is missing.";
                    continue;
                }

                $questionText = $row[0];
                $marks = (float) $row[1];
                $correctChar = strtoupper(trim($row[6] ?? '')); // A, B, C, or D
                $isPrevYear = strtolower(trim($row[7] ?? '')) === 'yes' || trim($row[7] ?? '') == '1';
                $year = $row[8] ?? null;

                $question = $questionBank->questions()->create([
                    'question_text' => $questionText,
                    'marks' => $marks,
                    'is_previous_year' => $isPrevYear,
                    'previous_year' => $year
                ]);

                // Create Options
                $options = [
                    'A' => $row[2] ?? null,
                    'B' => $row[3] ?? null,
                    'C' => $row[4] ?? null,
                    'D' => $row[5] ?? null,
                ];

                $hasCorrect = false;
                foreach ($options as $char => $text) {
                    if (empty($text)) continue;
                    
                    $isCorrect = ($char === $correctChar);
                    if ($isCorrect) $hasCorrect = true;

                    $question->options()->create([
                        'option_text' => $text,
                        'is_correct' => $isCorrect
                    ]);
                }

                if (!$hasCorrect) {
                    $errors[] = "Row $rowNumber: Correct option '$correctChar' not found in options A-D.";
                }

                $importCount++;
            }
            
            if (!empty($errors)) {
                DB::rollBack();
                fclose($stream);
                return redirect()->back()->with('error', 'Import failed due to errors: ' . implode(' ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? ' and more...' : ''));
            }

            DB::commit();
            fclose($stream);

            if ($importCount > 0) {
                app(\App\Services\SetupGuideService::class)->updateStep($questionBank->organization_id, 'has_questions', true);
            }

            return redirect()->back()->with('success', "Successfully imported $importCount questions.");
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($stream)) fclose($stream);
            return redirect()->back()->with('error', 'Error during processing: ' . $e->getMessage());
        }
    }

    /**
     * Authorize that the model belongs to the current user's organization.
     */
    protected function authorizeOrganization($model)
    {
        $orgId = Auth::user()->currentOrganization()->id;
        if ($model->organization_id !== $orgId) {
            abort(403, 'Unauthorized access to this resource.');
        }
    }
}
