<?php

namespace App\Actions\Influxdb\v2;

use App\Models\DataIntegration;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateClient
{
    use AsAction;

    public function handle(): Client
    {
        // Fetch the row where `type = 'InfluxDBv2'`
        $settings = DataIntegration::firstWhere('type', 'InfluxDBv2');

        $config = $settings->config ?? [];

        // Extract required fields, throwing if absent
        $url = $config['url'];
        $org = $config['org'];
        $bucket = $config['bucket'];
        $token = $config['token'];
        $verifySsl = $config['verify_ssl'];

        return new Client([
            'url' => $url,
            'token' => $token,
            'org' => $org,
            'bucket' => $bucket,
            'verifySSL' => $verifySsl,
            'precision' => WritePrecision::S,
        ]);
    }
}
