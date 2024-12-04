<?php

namespace App\Jobs\Influxdb\v2;

use App\Actions\Influxdb\v2\BuildPointData;
use App\Actions\Influxdb\v2\CreateClient;
use App\Models\Result;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

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
        $client = CreateClient::run();

        $writeApi = $client->createWriteApi();

        $point = BuildPointData::run($this->result);

        try {
            $writeApi->write($point);
        } catch (\Exception $e) {
            Log::error('Failed to write to InfluxDB.', [
                'error' => $e->getMessage(),
                'result_id' => $this->result->id,
            ]);

            $this->fail($e);
        }

        $writeApi->close();
    }
}
