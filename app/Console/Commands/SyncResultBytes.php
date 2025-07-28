<?php

namespace App\Console\Commands;

use App\Models\Result;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SyncResultBytes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-result-bytes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command syncs the downloaded and uploaded bytes for results.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all results that have data but missing bytes columns
        $results = Result::whereNotNull('data')
            ->where(function ($query) {
                $query->whereNull('downloaded_bytes')
                    ->orWhereNull('uploaded_bytes');
            })
            ->get();

        if ($results->isEmpty()) {
            $this->info('No results found that need bytes synchronization.');

            return 0;
        }

        $this->info("Found {$results->count()} results to sync.");

        $progressBar = $this->output->createProgressBar($results->count());
        $progressBar->start();

        $updated = 0;

        foreach ($results as $result) {
            $downloadBytes = Arr::get($result->data, 'download.bytes');
            $uploadBytes = Arr::get($result->data, 'upload.bytes');

            $needsUpdate = false;
            $updates = [];

            // Check if download bytes need to be updated
            if ($downloadBytes !== null && $result->downloaded_bytes === null) {
                $updates['downloaded_bytes'] = $downloadBytes;
                $needsUpdate = true;
            }

            // Check if upload bytes need to be updated
            if ($uploadBytes !== null && $result->uploaded_bytes === null) {
                $updates['uploaded_bytes'] = $uploadBytes;
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $result->update($updates);
                $updated++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Sync completed. {$updated} results updated successfully.");

        return 0;
    }
}
