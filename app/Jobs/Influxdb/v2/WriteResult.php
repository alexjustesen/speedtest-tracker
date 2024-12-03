<?php

namespace App\Jobs\Influxdb\v2;

use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;

class WriteResult implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $settings = app(DataIntegrationSettings::class);

        $client = new Client([
            'url' => $settings->influxdb_v2_url,
            'token' => $settings->influxdb_v2_token,
            'bucket' => $settings->influxdb_v2_bucket,
            'org' => $settings->influxdb_v2_org,
            'verifySSL' => $settings->influxdb_v2_verify_ssl,
            'precision' => WritePrecision::S,
        ]);

        $api = $client->createWriteApi();

        $data = [
            'name' => 'speedtest',
            'tags' => $this->result->formatTagsForInfluxDB2(),
            'fields' => $this->result->formatForInfluxDB2(),
            'time' => $this->result->created_at->timestamp,
        ];

        try {
            $api->write($data);
        } catch (\Exception $e) {
            Log::error('Failed to write to InfluxDB', [
                'error' => $e->getMessage(),
                'result_id' => $this->result->id,
            ]);

            $this->fail($e);
        }

        $api->close();
    }
}
