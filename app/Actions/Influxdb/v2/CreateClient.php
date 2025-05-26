<?php

namespace App\Actions\Influxdb\v2;

use App\Models\DataIntegrationSetting;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateClient
{
    use AsAction;

    public function handle(): Client
    {
        // Fetch the row where `type = 'InfluxDBv2'`
        $settings = DataIntegrationSetting::firstWhere('type', 'InfluxDBv2');

        return new Client([
            'url' => $settings->url,
            'token' => $settings->token,
            'org' => $settings->org,
            'bucket' => $settings->bucket,
            'verifySSL' => $settings->verify_ssl,
            'precision' => WritePrecision::S,
        ]);
    }
}
