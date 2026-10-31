<?php

namespace App\Console\Commands;

use App\Services\EntranceResultImportService;
use Illuminate\Console\Command;

class ImportEntranceResultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'results:import-docs {--force : Overwrite existing records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import entrance exam results from docs/Results/*.csv into the entrance_exam_results database table';

    /**
     * Execute the console command.
     */
    public function handle(EntranceResultImportService $service): int
    {
        $docsDir = base_path('docs/Results');
        if (!is_dir($docsDir)) {
            $this->error("Directory not found: {$docsDir}");
            return Command::FAILURE;
        }

        $files = [
            'APSC' => $docsDir . '/Results - APSC.csv',
            'UPSC' => $docsDir . '/Results - UPSC.csv',
            'NEET' => $docsDir . '/Results - NEET.csv',
            'JEE'  => $docsDir . '/Results - JEE.csv',
        ];

        $this->info("Starting Entrance Results Ingestion from {$docsDir}...");

        $totalInserted = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($files as $stream => $filePath) {
            if (!file_exists($filePath)) {
                $this->warn("File missing for {$stream}: {$filePath}");
                continue;
            }

            $this->line("Processing [{$stream}] from " . basename($filePath) . "...");
            $res = $service->importCsv($filePath, $stream, true);

            if ($res['success']) {
                $this->info("  ✓ {$stream}: {$res['inserted']} inserted, {$res['updated']} updated, {$res['skipped']} skipped.");
                $totalInserted += $res['inserted'];
                $totalUpdated += $res['updated'];
                $totalSkipped += $res['skipped'];

                if (!empty($res['warnings'])) {
                    foreach ($res['warnings'] as $w) {
                        $this->warn("    - {$w}");
                    }
                }
            } else {
                $this->error("  ✗ Failed {$stream}: {$res['message']}");
            }
        }

        $this->newLine();
        $this->info("Ingestion Complete: {$totalInserted} inserted, {$totalUpdated} updated, {$totalSkipped} skipped.");
        return Command::SUCCESS;
    }
}
