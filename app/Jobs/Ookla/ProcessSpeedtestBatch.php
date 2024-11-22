<?php

namespace App\Jobs\Ookla;

use App\Jobs\CheckForInternetConnectionJob;
use App\Jobs\SkipSpeedtestJob;
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
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Bus::batch([
            [
                new CheckForInternetConnectionJob($this->result),
                new SkipSpeedtestJob($this->result),
                new RunSpeedtestJob($this->result),
            ],
        ])->catch(function (Batch $batch, ?Throwable $e) {
            Log::error(sprintf('Speedtest batch "%s" failed for an unknown reason.', $batch->id));
        })->name('Ookla Speedtest')->dispatch();
    }
}
