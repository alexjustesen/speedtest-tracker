<?php

namespace App\Jobs\Ookla;

use App\Enums\ResultStatus;
use App\Events\SpeedtestCompleted;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CompleteSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

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
        $this->result->update([
            'status' => ResultStatus::Completed,
        ]);

        SpeedtestCompleted::dispatch($this->result);
    }
}
