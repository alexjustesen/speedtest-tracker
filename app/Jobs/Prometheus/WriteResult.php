<?php

namespace App\Jobs\Prometheus;

use App\Models\Result;
use App\Services\Prometheus\PrometheusRemoteWriteService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class WriteResult implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Result $result,
    ) {}

    public function handle(PrometheusRemoteWriteService $service): void
    {
        try {
            $service->send($this->result);
        } catch (Exception $e) {
            Log::error('Failed to write to Prometheus remote write endpoint.', [
                'error' => $e->getMessage(),
                'result_id' => $this->result->id,
            ]);

            $this->fail($e);
        }
    }
}
