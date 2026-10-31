<?php

namespace App\Services;

use App\Models\EntranceExamResult;
use App\Models\StudentApplication;
use App\Models\ApplicationForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntranceResultImportService
{
    /**
     * Import a CSV file for a given stream or auto-detected stream
     */
    public function importCsv(string $filePath, ?string $selectedStream = null, bool $autoLink = true): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return [
                'success' => false,
                'message' => 'The uploaded file could not be read.',
                'errors' => ['File not accessible on server.']
            ];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [
                'success' => false,
                'message' => 'Unable to open CSV file.',
                'errors' => ['Cannot open file stream.']
            ];
        }

        // Detect and strip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read header line
        $rawHeaders = fgetcsv($handle);
        if (!$rawHeaders || empty(array_filter($rawHeaders))) {
            fclose($handle);
            return [
                'success' => false,
                'message' => 'The uploaded CSV file is empty or missing headers.',
                'errors' => ['Missing CSV header row.']
            ];
        }

        $headerMap = $this->mapHeaders($rawHeaders);
        if (!isset($headerMap['roll_no'])) {
            fclose($handle);
            return [
                'success' => false,
                'message' => 'Invalid CSV structure: Missing "ROLL NO" column.',
                'errors' => ['Could not find a valid ROLL NO column header.']
            ];
        }

        if (!isset($headerMap['marks'])) {
            fclose($handle);
            return [
                'success' => false,
                'message' => 'Invalid CSV structure: Missing "MARKS" or "SCORE" column.',
                'errors' => ['Could not find a valid MARKS or SCORE column header.']
            ];
        }

        // Cache application forms for quick ID resolution
        $forms = ApplicationForm::select('id', 'application_name', 'applied_for_options')->get();
        $civilFormId = $forms->first(fn($f) => str_contains($f->application_name ?? '', 'Civil'))?->id;
        $neetJeeFormId = $forms->first(fn($f) => str_contains($f->application_name ?? '', 'NEET') || str_contains($f->application_name ?? '', 'JEE'))?->id;

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $warnings = [];
        $errors = [];
        $rowNum = 1; // 1 is header
        $seenRollsInBatch = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;

                // Skip completely empty rows
                if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                    continue;
                }

                $rollNo = trim((string)($row[$headerMap['roll_no']] ?? ''));
                if ($rollNo === '') {
                    $warnings[] = "Row {$rowNum}: Skipped due to missing Roll Number.";
                    $skipped++;
                    continue;
                }

                // Check for duplicate roll number within the current file batch
                if (isset($seenRollsInBatch[$rollNo])) {
                    $candidateName = isset($headerMap['name']) ? trim((string)($row[$headerMap['name']] ?? '')) : 'Unknown';
                    $warnings[] = "Row {$rowNum}: Duplicate Roll Number '{$rollNo}' for candidate '{$candidateName}'. Skipped to preserve unique roll numbers. (Will be fixed when roll number is corrected).";
                    $skipped++;
                    continue;
                }
                $seenRollsInBatch[$rollNo] = true;

                $candidateName = isset($headerMap['name']) ? trim(strtoupper((string)($row[$headerMap['name']] ?? ''))) : 'CANDIDATE';
                $rawScore = isset($headerMap['marks']) ? trim((string)($row[$headerMap['marks']] ?? '0')) : '0';
                $marks = is_numeric($rawScore) ? (float)$rawScore : 0.0;

                // Rank / SL NO
                $rankVal = null;
                if (isset($headerMap['rank'])) {
                    $rStr = trim((string)($row[$headerMap['rank']] ?? ''));
                    if (is_numeric($rStr)) {
                        $rankVal = (int)$rStr;
                    }
                }

                // Phone Number
                $phoneVal = null;
                if (isset($headerMap['phone'])) {
                    $pStr = trim((string)($row[$headerMap['phone']] ?? ''));
                    if ($pStr !== '' && strtoupper($pStr) !== 'N/A' && strtoupper($pStr) !== 'NA') {
                        $phoneVal = $pStr;
                    }
                }

                // District
                $districtVal = null;
                if (isset($headerMap['district'])) {
                    $dStr = trim(strtoupper((string)($row[$headerMap['district']] ?? '')));
                    if ($dStr !== '' && $dStr !== 'N/A') {
                        $districtVal = $dStr;
                    }
                }

                // Resolve Stream: User selected > Inferred by prefix > Default
                $stream = $this->resolveStream($selectedStream, $rollNo);

                // Auto-link to student_applications
                $applicationId = null;
                $formId = null;

                if ($autoLink) {
                    $matchedApp = StudentApplication::where('roll_no', $rollNo)
                        ->orWhereHas('application_student', function ($q) use ($rollNo, $phoneVal) {
                            $q->where('phone_number', $phoneVal)->whereNotNull('phone_number');
                        })
                        ->first();

                    if ($matchedApp) {
                        $applicationId = $matchedApp->id;
                        $formId = $matchedApp->application_form_id;

                        // Backfill phone or district if missing
                        if (!$phoneVal && $matchedApp->application_student?->phone_number) {
                            $phoneVal = $matchedApp->application_student->phone_number;
                        }
                        if (!$districtVal) {
                            $districtVal = $matchedApp->examination_center 
                                ?? $matchedApp->application_address?->permanent_district 
                                ?? $matchedApp->application_student?->examination_center;
                            if ($districtVal) {
                                $districtVal = strtoupper(trim($districtVal));
                            }
                        }
                    }
                }

                // Fallback Form ID
                if (!$formId) {
                    $formId = in_array($stream, ['APSC', 'UPSC']) ? $civilFormId : $neetJeeFormId;
                }

                // Update or Create Result Record
                $existing = EntranceExamResult::where('roll_no', $rollNo)->first();
                $payload = [
                    'student_application_id' => $applicationId,
                    'application_form_id' => $formId,
                    'stream' => $stream,
                    'candidate_name' => $candidateName,
                    'phone_number' => $phoneVal,
                    'district' => $districtVal,
                    'marks' => $marks,
                    'rank' => $rankVal,
                    'is_published' => true,
                    'published_at' => now(),
                    'raw_payload' => $row,
                ];

                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                } else {
                    EntranceExamResult::create(array_merge($payload, ['roll_no' => $rollNo]));
                    $inserted++;
                }
            }

            DB::commit();
            fclose($handle);

            return [
                'success' => true,
                'message' => "Successfully processed CSV: {$inserted} inserted, {$updated} updated, {$skipped} skipped.",
                'stream' => $selectedStream,
                'total_processed' => $inserted + $updated,
                'inserted' => $inserted,
                'updated' => $updated,
                'skipped' => $skipped,
                'warnings' => $warnings,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            Log::error('Entrance Result Import Failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'success' => false,
                'message' => 'Database error during CSV import: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Normalize and map CSV column headers to database attributes
     */
    protected function mapHeaders(array $headers): array
    {
        $map = [];
        foreach ($headers as $index => $header) {
            $h = strtoupper(trim((string)$header));
            // Remove special characters and excess spaces
            $h = preg_replace('/[^A-Z0-9]/', '', $h);

            if (in_array($h, ['ROLLNO', 'ROLLNUMBER', 'ROLL', 'ROLLS'])) {
                $map['roll_no'] = $index;
            } elseif (in_array($h, ['NAME', 'STUDENTNAME', 'CANDIDATENAME', 'FULLNAME', 'APPLICANTNAME'])) {
                $map['name'] = $index;
            } elseif (in_array($h, ['SCORE', 'MARKS', 'MARKSOBTAINED', 'TOTALMARKS', 'SCORES'])) {
                $map['marks'] = $index;
            } elseif (in_array($h, ['PHNO', 'PHONENO', 'PHONE', 'PHONENUMBER', 'MOBILE', 'MOBILENO', 'CONTACT'])) {
                $map['phone'] = $index;
            } elseif (in_array($h, ['DISTRICT', 'DIST', 'EXAMDISTRICT', 'CENTRE', 'CENTER'])) {
                $map['district'] = $index;
            } elseif (in_array($h, ['SLNO', 'RANK', 'SERIALNO', 'SRNO', 'POSITION'])) {
                $map['rank'] = $index;
            }
        }
        return $map;
    }

    /**
     * Resolve stream from explicit selection or roll number prefix
     */
    public function resolveStream(?string $selectedStream, string $rollNo): string
    {
        if ($selectedStream && strtolower($selectedStream) !== 'all') {
            return EntranceExamResult::normalizeStream($selectedStream);
        }

        // Infer by roll number prefix
        $roll = trim($rollNo);
        if (str_starts_with($roll, '261')) return 'UPSC';
        if (str_starts_with($roll, '262')) return 'APSC';
        if (str_starts_with($roll, '263')) return 'JEE';
        if (str_starts_with($roll, '64')) return 'NEET';

        return 'GENERAL';
    }

    /**
     * Generate standard CSV sample string for stream download
     */
    public function getTemplateCsv(string $stream): string
    {
        $st = strtoupper(trim($stream));
        $header = "ROLL NO,CANDIDATE NAME,MARKS,DISTRICT,PH NO,RANK\n";

        return match ($st) {
            'APSC' => $header .
                "2620001,JOHN BRAHMA,88.75,KOKRAJHAR,9876543210,1\n" .
                "2620002,PRANAB ROY,85.50,CHIRANG,9876543211,2\n" .
                "2620003,ANJALI BASUMATARY,82.25,BAKSA,9876543212,3\n",
            'UPSC' => $header .
                "2610001,KUSHAL BASNET,93.75,KOKRAJHAR,8473063674,1\n" .
                "2610002,IMANUEL BORO,90.00,UDALGURI,9101517108,2\n" .
                "2610003,BEDH PRAKASH NEWAR,89.25,TAMULPUR,9220774033,3\n",
            'NEET' => $header .
                "64001,GAURAB ROY,54,KOKRAJHAR,9876543220,1\n" .
                "64002,AKASH BISWAS,50,KOKRAJHAR,9876543221,2\n" .
                "64003,RAJIB SARKAR,50,CHIRANG,9876543222,3\n",
            'JEE' => $header .
                "26301,SAYAN DEBNATH,53,KOKRAJHAR,9876543230,1\n" .
                "26302,BISHAL SWARGIARY,47,BAKSA,9876543231,2\n" .
                "26303,DEEP BAISHYA,35,TAMULPUR,9876543232,3\n",
            default => $header .
                "2620001,SAMPLE CANDIDATE,75.00,KOKRAJHAR,9876543210,1\n"
        };
    }
}
