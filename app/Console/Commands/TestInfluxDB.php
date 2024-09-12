<?php

namespace App\Console\Commands;

use App\Models\Result;
use App\Settings\MetricsSettings;
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
    public function handle(MetricsSettings $settings): void
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
        }
    }
}
