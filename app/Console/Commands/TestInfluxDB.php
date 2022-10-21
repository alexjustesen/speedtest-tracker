<?php

namespace App\Console\Commands;

use App\Models\Result;
use App\Settings\InfluxDbSettings;
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
     *
     * @return int
     */
    public function handle(InfluxDbSettings $settings)
    {
        $influxdb = [
            'enabled' => $settings->v2_enabled,
            'url' => optional($settings)->v2_url,
            'org' => optional($settings)->v2_org,
            'bucket' => optional($settings)->v2_bucket,
            'token' => optional($settings)->v2_token,
        ];

        if ($influxdb['enabled'] == true) {
            $result = Result::factory()->create();
        }

        return 0;
    }
}
