<?php

namespace App\Jobs;

use App\Models\Result;
use App\Settings\InfluxDbSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use InfluxDB2\Client;

class SendDataToInfluxDbV2 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $result;

    public $settings;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Result $result, InfluxDbSettings $settings)
    {
        $this->result = $result;

        $this->settings = $settings;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $influxdb = [
            'enabled' => $this->settings->v2_enabled,
            'url' => optional($this->settings)->v2_url,
            'org' => optional($this->settings)->v2_org,
            'bucket' => optional($this->settings)->v2_bucket,
            'token' => optional($this->settings)->v2_token,
            'verifySSL' => $this->settings->v2_verify_ssl,
        ];

        $client = new Client([
            'url' => $influxdb['url'],
            'token' => $influxdb['token'],
            'bucket' => $influxdb['bucket'],
            'org' => $influxdb['org'],
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
            Log::error($e);
        }

        $writeApi->close();
    }
}
