<?php

namespace App\Console\Commands;

use App\Jobs\InfluxDBv2\WriteSpeedtestResult;
use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class SendAllResultsToInfluxDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-all-results-to-influxdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all speed test results to InfluxDB.';

    /**
     * Execute the console command.
     */
    public function handle(DataIntegrationSettings $settings): void
    {

        // Get the count of all Result records
        $totalResults = Result::count();

        if ($totalResults === 0) {
            $this->info('No results found to send to InfluxDB.');

            return;
        }

        $this->info("Found {$totalResults} results to be sent to InfluxDB.");

        // Fetch all results
        $results = Result::all();

        // Iterate through all results and dispatch the InfluxDB job for each
        foreach ($results as $result) {
            WriteSpeedtestResult::dispatch($result);
            $this->info("Dispatched result ID {$result->id} to InfluxDB.");
        }

        $this->info('Finished sending all results to InfluxDB.');

        Notification::make()
            ->title('Success')
            ->body('All old results have been dispatched to InfluxDB successfully!')
            ->success()
            ->send();
    }
}
