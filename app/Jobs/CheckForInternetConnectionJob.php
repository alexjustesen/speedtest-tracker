<?php

namespace App\Jobs;

use App\Actions\CheckInternetConnection;
use App\Enums\ResultStatus;
use App\Events\SpeedtestChecking;
use App\Events\SpeedtestFailed;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class CheckForInternetConnectionJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
    ) {}

    /**
     * Get the middleware the job should pass through.
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
            'status' => ResultStatus::Checking,
        ]);

        SpeedtestChecking::dispatch($this->result);

        if (CheckInternetConnection::run() !== false) {
            return;
        }

        $this->result->update([
            'data->type' => 'log',
            'data->level' => 'error',
            'data->message' => 'Failed to connect to the internet.',
            'status' => ResultStatus::Failed,
        ]);

        SpeedtestFailed::dispatch($this->result);

        $this->batch()->cancel();
    }
}
