<?php

namespace App\Jobs\Ookla;

use App\Actions\UpdateNextRun;
use App\Jobs\CheckForInternetConnectionJob;
use App\Models\Result;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessSpeedtestBatch implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
        public array $scheduleOptions = [],
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $result = $this->result;
        $skipIps = $this->scheduleOptions['skip_ips'] ?? [];
        $interface = $this->scheduleOptions['interface'] ?? null;

        Bus::batch([
            [
                new CheckForInternetConnectionJob($this->result),
                new SkipSpeedtestJob($this->result, $skipIps),
                new SelectSpeedtestServerJob($this->result),
                new RunSpeedtestJob($this->result, $interface),
                new BenchmarkSpeedtestJob($this->result),
                new CompleteSpeedtestJob($this->result),
            ],
        ])->catch(function (Batch $batch, ?Throwable $e) {
            Log::error(sprintf('Speedtest batch "%s" failed for an unknown reason.', $batch->id));
        })
            ->finally(function () use ($result) {
                if ($result && $result->schedule) {
                    UpdateNextRun::dispatch($result->schedule);
                }
            })
            ->name('Ookla Speedtest')
            ->dispatch();
    }
}
