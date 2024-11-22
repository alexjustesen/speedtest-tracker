<?php

namespace App\Jobs;

use App\Actions\GetExternalIpAddress;
use App\Enums\ResultStatus;
use App\Events\SpeedtestChecking;
use App\Events\SpeedtestFailed;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $this->result->update([
            'status' => ResultStatus::Checking,
        ]);

        SpeedtestChecking::dispatch($this->result);

        if (GetExternalIpAddress::run() !== false) {
            return;
        }

        $this->result->update([
            'data->type' => 'log',
            'data->level' => 'error',
            'data->message' => 'Failed to fetch external IP address, server is likely unable to connect to the internet.',
            'status' => ResultStatus::Failed,
        ]);

        SpeedtestFailed::dispatch($this->result);

        $this->batch()->cancel();
    }
}
