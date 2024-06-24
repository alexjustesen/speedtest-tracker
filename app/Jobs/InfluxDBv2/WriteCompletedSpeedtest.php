<?php

namespace App\Jobs\InfluxDBv2;

use App\Models\Result;
use App\Settings\InfluxDbSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use InfluxDB2\Client;

class WriteCompletedSpeedtest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Result $result,
        public InfluxDbSettings $settings,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $influxdb = [
            'enabled' => $this->settings->v2_enabled,
            'url' => $this->settings?->v2_url,
            'org' => $this->settings?->v2_org,
            'bucket' => $this->settings?->v2_bucket,
            'token' => $this->settings?->v2_token,
            'verifySSL' => $this->settings->v2_verify_ssl,
        ];

        $client = new Client([
            'url' => $influxdb['url'],
            'token' => $influxdb['token'],
            'bucket' => $influxdb['bucket'],
            'org' => $influxdb['org'],
            'verifySSL' => $influxdb['verifySSL'],
            'precision' => \InfluxDB2\Model\WritePrecision::S,
        ]);

        $writeApi = $client->createWriteApi();

        $dataArray = [
            'name' => 'speedtest',
            'tags' => $this->result->formatTagsForInfluxDB2(),
            'fields' => $this->result->formatForInfluxDB2(),
            'time' => strtotime($this->result->created_at),
        ];

        try {
            $writeApi->write($dataArray);
        } catch (\Exception $e) {
            Log::info($e);

            $this->fail();
        }

        $writeApi->close();
    }
}
