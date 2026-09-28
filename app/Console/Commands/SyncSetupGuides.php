<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organization;
use App\Services\SetupGuideService;

class SyncSetupGuides extends Command
{
    protected $signature = 'setup:sync {organization_id?}';
    protected $description = 'Sync setup guides for organizations';

    public function handle()
    {
        $orgId = $this->argument('organization_id');
        $service = app(SetupGuideService::class);

        if ($orgId) {
            $this->info("Syncing setup guide for organization ID: $orgId");
            $service->sync($orgId);
        } else {
            $orgs = Organization::all();
            $this->info("Syncing setup guides for " . $orgs->count() . " organizations...");
            foreach ($orgs as $org) {
                $service->sync($org->id);
            }
        }

        $this->info('Sync complete!');
    }
}
