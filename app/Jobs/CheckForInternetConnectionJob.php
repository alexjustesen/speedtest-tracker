<?php

namespace App\Jobs;

use App\Actions\PingHostname;
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

        $ping = PingHostname::run();

        if ($ping->isSuccess()) {
            return;
        }

        $message = sprintf('Failed to connected to hostname "%s". Error received "%s".', $ping->getHost(), $ping->error()?->value);

        $this->result->update([
            'data->type' => 'log',
            'data->level' => 'error',
            'data->message' => $message,
            'status' => ResultStatus::Failed,
        ]);

        SpeedtestFailed::dispatch($this->result);

        $this->batch()->cancel();
    }
}
