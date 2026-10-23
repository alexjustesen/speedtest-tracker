<?php

namespace App\Actions\Ookla;

use App\Models\Result;
use Lorisleiva\Actions\Concerns\AsAction;

class RetrySpeedtest
{
    use AsAction;

    /**
     * Dispatch the next attempt if retries are enabled and attempts remain.
     */
    public function handle(Result $result, int $attempt): void
    {
        $retryTimes = (int) config('speedtest.retry_times');

        // Retrying is disabled entirely.
        if ($retryTimes === 0) {
            return;
        }

        // No attempts remain.
        if ($attempt > $retryTimes) {
            return;
        }

        RunSpeedtest::run(
            scheduled: $result->scheduled,
            serverId: $result->server_id,
            dispatchedBy: $result->dispatched_by,
            attempt: $attempt + 1,
        );
    }
}
