<?php

namespace App\Console\Commands;

use App\Jobs\InfluxDBv2\WriteCompletedSpeedtest;
use App\Models\Result;
use App\Settings\MetricsSettings;
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
    public function handle(MetricsSettings $settings): void
    {
        // Check if InfluxDB is enabled
        if (! $settings->influxdb_v2_enabled) {
            $this->error('InfluxDB is not enabled in the settings.');

            return;
        }

        // Get the count of all Result records
        $totalResults = Result::count();

        if ($totalResults === 0) {
            $this->info('No results found to send to InfluxDB.');

            return;
        }

        $this->info("Found {$totalResults} results to be sent to InfluxDB.");

        // Iterate through all results in chunks of 100 to avoid memory issues
        Result::chunk(100, function ($results) use ($settings) {
            foreach ($results as $result) {
                // Dispatch the InfluxDB job for each result
                WriteCompletedSpeedtest::dispatch($result, $settings);
                $this->info("Dispatched result ID {$result->id} to InfluxDB.");
            }
        });

        $this->info('Finished sending all results to InfluxDB.');
    }
}
