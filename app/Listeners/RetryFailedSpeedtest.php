<?php

namespace App\Listeners;

use App\Actions\Ookla\RetrySpeedtest;
use App\Events\SpeedtestFailed;
use Illuminate\Contracts\Queue\ShouldQueue;

class RetryFailedSpeedtest implements ShouldQueue
{
    /**
     * Determine the number of seconds before the retry should be dispatched.
     */
    public function withDelay(SpeedtestFailed $event): int
    {
        return max(0, (int) config('speedtest.retry_delay'));
    }

    /**
     * Determine whether the listener should be queued.
     */
    public function shouldQueue(SpeedtestFailed $event): bool
    {
        return (int) config('speedtest.retry_times') > 0;
    }

    /**
     * Handle the event.
     */
    public function handle(SpeedtestFailed $event): void
    {
        RetrySpeedtest::run($event->result, $event->attempt);
    }
}
