<?php

namespace App\Console\Commands;

use App\Jobs\InfluxDBv2\WriteSpeedtestResult;
use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class TestInfluxDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-influxdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write a test log to InfluxDB to make sure the config works.';

    /**
     * Execute the console command.
     */
    public function handle(DataIntegrationSettings $settings): void
    {
        $influxdb = [
            'enabled' => $settings->influxdb_v2_enabled,
            'url' => $settings?->influxdb_v2_url,
            'org' => $settings?->influxdb_v2_org,
            'bucket' => $settings?->influxdb_v2_bucket,
            'token' => $settings?->influxdb_v2_token,
        ];

        if ($influxdb['enabled'] == true) {
            // Create a test result if InfluxDB is enabled
            $result = Result::factory()->create();

            // Dispatch the job to write the result to InfluxDB
            dispatch(new WriteSpeedtestResult($result, $settings));

            // Output a success message
            $this->info('Test result created and job dispatched to InfluxDB.');

        }

        Notification::make()
            ->title('Success')
            ->body('A test log has been sent to InfluxDB, Check in InfluxDB if the data is received!')
            ->success()
            ->send();
    }
}
