<?php

namespace App\Jobs\InfluxDBv2;

use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use InfluxDB2\Client;

class WriteSpeedtestResult implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Indicates if the job should run.
     */
    protected bool $enabled;

    protected DataIntegrationSettings $settings;

    /**
     * Create a new job instance.
     */
    public function __construct(public Result $result)
    {
        $this->settings = app(DataIntegrationSettings::class);
        $this->enabled = $this->settings->influxdb_v2_enabled;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! $this->enabled) {
            return;
        }

        // Now $this->settings is available in the handle method
        $client = new Client([
            'url' => $this->settings->influxdb_v2_url,
            'token' => $this->settings->influxdb_v2_token,
            'bucket' => $this->settings->influxdb_v2_bucket,
            'org' => $this->settings->influxdb_v2_org,
            'verifySSL' => $this->settings->influxdb_v2_verify_ssl,
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
            Log::info('Writing to InfluxDB', ['data' => $dataArray]);

            $writeApi->write($dataArray);
        } catch (\Exception $e) {
            Log::error('Failed to write to InfluxDB', [
                'error' => $e->getMessage(),
                'result_id' => $this->result->id,
            ]);

            $this->fail($e);
        } finally {
            $writeApi->close();
        }
    }
}
