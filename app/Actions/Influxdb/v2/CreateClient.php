<?php

namespace App\Actions\Influxdb\v2;

use App\Settings\DataIntegrationSettings;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateClient
{
    use AsAction;

    public function handle(): Client
    {
        $settings = app(DataIntegrationSettings::class);

        return new Client([
            'url' => $settings->influxdb_v2_url,
            'token' => $settings->influxdb_v2_token,
            'bucket' => $settings->influxdb_v2_bucket,
            'org' => $settings->influxdb_v2_org,
            'verifySSL' => $settings->influxdb_v2_verify_ssl,
            'precision' => WritePrecision::S,
        ]);
    }
}
