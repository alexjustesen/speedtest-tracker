<?php

namespace App\Actions\InfluxDBv2;

use App\Jobs\InfluxDBv2\WriteSpeedtestResult;
use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class SendAllResultsToInfluxDB
{
    use AsAction;

    /**
     * Execute the action.
     */
    public function handle(): void
    {
        // Resolve the settings within the action
        $settings = app(DataIntegrationSettings::class);

        // Get the count of all Result records
        $totalResults = Result::count();

        if ($totalResults === 0) {
            // Notify that there are no results to export
            Notification::make()
                ->title('No Results to Export')
                ->body('There are no speed test results available to send to InfluxDB.')
                ->warning()
                ->send();

            return;
        }

        // Notify that the export is starting
        Notification::make()
            ->title('Export Started')
            ->body("Found {$totalResults} results to be sent to InfluxDB. Export process has started.")
            ->info()
            ->send();

        // Fetch all results
        $results = Result::all();

        // Dispatch the job for each result
        foreach ($results as $result) {
            WriteSpeedtestResult::dispatch($result);
        }
    }
}
