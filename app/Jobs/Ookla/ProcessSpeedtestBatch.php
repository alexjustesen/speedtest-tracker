<?php

namespace App\Jobs\Ookla;

use App\Jobs\CheckForInternetConnectionJob;
use App\Jobs\SkipSpeedtestJob;
use App\Models\Result;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
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
        $batch = Bus::batch([
            [
                new CheckForInternetConnectionJob($this->result),
                new SkipSpeedtestJob($this->result),
                new RunSpeedtestJob($this->result),
            ],
        ])->catch(function (Throwable $e) {
            // TODO: A job within the chain failed, do something about it.
        })->dispatch();
    }
}
