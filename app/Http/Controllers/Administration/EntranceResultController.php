<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\EntranceExamResult;
use App\Services\EntranceResultImportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntranceResultController extends Controller
{
    protected EntranceResultImportService $importService;

    public function __construct(EntranceResultImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Data Verification & Reconciliation against online submitted applications
     */
    public function reconciliation(Request $request)
    {
        $stream = $request->query('stream', 'ALL');
        $search = $request->query('search');

        $query = EntranceExamResult::with([
            'student_application.application_student',
            'student_application.application_address',
            'application_by_roll.application_student',
            'application_by_roll.application_address',
        ]);

        if ($stream && strtoupper($stream) !== 'ALL') {
            $query->where('stream', strtoupper($stream));
        }

        if ($search) {
            $query->search($search);
        }

        $query->orderBy('stream', 'asc')
              ->orderByRaw('CASE WHEN `rank` IS NOT NULL THEN `rank` ELSE 999999 END asc')
              ->orderBy('marks', 'desc');

        $results = $query->paginate(50)->withQueryString();

        $results->getCollection()->transform(function ($item) {
            $app = $item->student_application ?? $item->application_by_roll;

            $upName = trim($item->candidate_name ?? '');
            $upPhone = $item->phone_number ? trim($item->phone_number) : null;
            $upDistrict = $item->district ? trim($item->district) : null;

            $hasDb = ($app !== null);
            $dbName = $app?->application_student?->full_name ? trim($app->application_student->full_name) : null;
            $dbPhone = $app?->application_student?->phone_number ? trim($app->application_student->phone_number) : null;
            $rawCenter = $app?->examination_center ?? $app?->application_student?->examination_center ?? $app?->application_address?->permanent_district;
            $dbDistrict = $rawCenter ? trim($rawCenter) : null;

            // 1. Name Match Evaluation
            $nameStatus = 'no_db';
            if ($hasDb) {
                $normUp = strtoupper((string)preg_replace('/[^A-Za-z0-9]/', '', $upName));
                $normDb = strtoupper((string)preg_replace('/[^A-Za-z0-9]/', '', (string)$dbName));
                if ($normUp === $normDb && $normUp !== '') {
                    $nameStatus = 'matched';
                } elseif (str_contains($normDb, $normUp) || str_contains($normUp, $normDb)) {
                    $nameStatus = 'partial';
                } else {
                    $nameStatus = 'mismatch';
                }
            }

            // 2. Phone Match Evaluation
            $phoneStatus = 'no_db';
            if ($hasDb) {
                $digitsUp = preg_replace('/[^0-9]/', '', (string)$upPhone);
                $digitsDb = preg_replace('/[^0-9]/', '', (string)$dbPhone);
                if (empty($digitsUp) || empty($digitsDb)) {
                    $phoneStatus = 'na';
                } else {
                    $last10Up = substr($digitsUp, -10);
                    $last10Db = substr($digitsDb, -10);
                    $phoneStatus = ($last10Up === $last10Db) ? 'matched' : 'mismatch';
                }
            }

            // 3. District Match Evaluation
            $districtStatus = 'no_db';
            if ($hasDb) {
                if (!$upDistrict || !$dbDistrict) {
                    $districtStatus = 'na';
                } else {
                    $normUpDist = strtoupper($upDistrict);
                    $normDbDist = strtoupper($dbDistrict);
                    if ($normUpDist === $normDbDist || str_contains($normDbDist, $normUpDist) || str_contains($normUpDist, $normDbDist)) {
                        $districtStatus = 'matched';
                    } else {
                        $districtStatus = 'mismatch';
                    }
                }
            }

            $item->recon = (object)[
                'has_db' => $hasDb,
                'db_name' => $dbName,
                'db_phone' => $dbPhone,
                'db_district' => $dbDistrict,
                'name_status' => $nameStatus,
                'phone_status' => $phoneStatus,
                'district_status' => $districtStatus,
            ];

            return $item;
        });

        $totalCount = EntranceExamResult::count();
        $linkedCount = EntranceExamResult::whereNotNull('student_application_id')
            ->orWhereIn('roll_no', \App\Models\StudentApplication::whereNotNull('roll_no')->where('roll_no', '!=', '')->pluck('roll_no'))
            ->count();
        $unlinkedCount = $totalCount - $linkedCount;

        return view('administration.entrance-results.reconciliation', compact(
            'results',
            'stream',
            'search',
            'totalCount',
            'linkedCount',
            'unlinkedCount'
        ));
    }

    /**
     * Display a paginated listing of entrance results with filters and stats
     */
    public function index(Request $request)
    {
        $stream = $request->query('stream', 'ALL');
        $search = $request->query('search');
        $district = $request->query('district');

        $query = EntranceExamResult::with('student_application.application_student');

        if ($stream && strtoupper($stream) !== 'ALL') {
            $query->where('stream', strtoupper($stream));
        }

        if ($district && strtoupper($district) !== 'ALL') {
            $query->where('district', strtoupper($district));
        }

        if ($search) {
            $query->search($search);
        }

        // Sorting: default by stream, then rank, then marks DESC
        $query->orderBy('stream', 'asc')
              ->orderByRaw('CASE WHEN `rank` IS NOT NULL THEN `rank` ELSE 999999 END asc')
              ->orderBy('marks', 'desc');

        $results = $query->paginate(50)->withQueryString();

        // Summary Statistics
        $stats = [
            'total' => EntranceExamResult::count(),
            'apsc'  => EntranceExamResult::where('stream', 'APSC')->count(),
            'upsc'  => EntranceExamResult::where('stream', 'UPSC')->count(),
            'neet'  => EntranceExamResult::where('stream', 'NEET')->count(),
            'jee'   => EntranceExamResult::where('stream', 'JEE')->count(),
            'avg'   => round((float) EntranceExamResult::avg('marks'), 2),
            'max'   => round((float) EntranceExamResult::max('marks'), 2),
        ];

        // Available districts for filter dropdown
        $districts = EntranceExamResult::whereNotNull('district')
            ->where('district', '!=', '')
            ->distinct()
            ->orderBy('district')
            ->pluck('district');

        return view('administration.entrance-results.index', compact(
            'results',
            'stats',
            'stream',
            'search',
            'district',
            'districts'
        ));
    }

    /**
     * Handle Stream-Wise CSV Upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'stream' => ['required', 'string', Rule::in(['APSC', 'UPSC', 'NEET', 'JEE', 'ALL'])],
            'result_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'auto_link' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('result_file');
        $selectedStream = $request->input('stream');
        $autoLink = $request->boolean('auto_link', true);

        $result = $this->importService->importCsv($file->getRealPath(), $selectedStream, $autoLink);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['message'])
                ->with('import_errors', $result['errors'] ?? []);
        }

        $msg = $result['message'];
        if (!empty($result['warnings'])) {
            return redirect()->back()
                ->with('success', $msg)
                ->with('import_warnings', $result['warnings']);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Download Standard Structured CSV Template
     */
    public function template(Request $request)
    {
        $stream = $request->query('stream', 'APSC');
        $csvContent = $this->importService->getTemplateCsv($stream);
        $fileName = "entrance_results_template_" . strtolower($stream) . ".csv";

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Update an individual result (e.g. to fix roll number or candidate name)
     */
    public function update(Request $request, $id)
    {
        $result = EntranceExamResult::findOrFail($id);

        $validated = $request->validate([
            'roll_no' => ['required', 'string', 'max:30', Rule::unique('entrance_exam_results', 'roll_no')->ignore($result->id)],
            'candidate_name' => ['required', 'string', 'max:150'],
            'stream' => ['required', 'string', Rule::in(['APSC', 'UPSC', 'NEET', 'JEE'])],
            'marks' => ['required', 'numeric', 'min:0', 'max:500'],
            'rank' => ['nullable', 'integer', 'min:1'],
            'district' => ['nullable', 'string', 'max:60'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'qualification_status' => ['required', Rule::in(['qualified', 'waitlisted', 'not_qualified', 'absent'])],
        ]);

        $result->update($validated);

        return redirect()->back()->with('success', "Result for Roll No {$result->roll_no} updated successfully.");
    }

    /**
     * Delete an individual result record
     */
    public function destroy($id)
    {
        $result = EntranceExamResult::findOrFail($id);
        $roll = $result->roll_no;
        $result->delete();

        return redirect()->back()->with('success', "Result record for Roll No {$roll} deleted.");
    }

    /**
     * Printable Result Scorecard (Admin only)
     */
    public function printScorecard($rollNo)
    {
        $result = EntranceExamResult::with('student_application.application_student')
            ->where('roll_no', trim($rollNo))
            ->firstOrFail();

        return view('administration.entrance-results.print', compact('result'));
    }
}
