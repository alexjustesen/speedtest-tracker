<?php

namespace App\Jobs\Speedtests;

use App\Enums\ResultStatus;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestRunning;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Runs a speedtest for the services measured by the application itself
 * (Cloudflare, Yandex, iPerf3 and file download).
 */
class RunSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
    ) {}

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->result->update([
            'status' => ResultStatus::Running,
        ]);

        SpeedtestRunning::dispatch($this->result);

        try {
            $speedtest = $this->result->service->speedtest()
                ?? throw new RuntimeException(sprintf('The "%s" service can not be run by this job.', $this->result->service->value));

            $output = $speedtest->run(Arr::get($this->result->data, 'server.host'));
        } catch (Throwable $e) {
            Log::error(sprintf('%s speedtest failed.', $this->result->service->getLabel()), [
                'result_id' => $this->result->id,
                'error' => $e->getMessage(),
            ]);

            $this->result->update([
                'data->type' => 'log',
                'data->level' => 'error',
                'data->message' => $e->getMessage(),
                'status' => ResultStatus::Failed,
            ]);

            $this->batch()?->cancel();

            SpeedtestFailed::dispatch($this->result);

            return;
        }

        $this->result->update([
            'ping' => Arr::get($output, 'ping.latency'),
            'download' => Arr::get($output, 'download.bandwidth'),
            'upload' => Arr::get($output, 'upload.bandwidth'),
            'download_bytes' => Arr::get($output, 'download.bytes'),
            'upload_bytes' => Arr::get($output, 'upload.bytes'),
            'data' => $output,
        ]);
    }
}
