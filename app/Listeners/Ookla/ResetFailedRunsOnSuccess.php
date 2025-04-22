<?php

namespace App\Listeners\Ookla;

use App\Events\SpeedtestCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetFailedRunsOnSuccess implements ShouldQueue
{
    /**
     * Handle a successful speedtest and reset the failed_runs counter on its schedule,
     * but only if max_retries is greater than zero.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $schedule = $event->result->schedule;

        // Nothing to do if there's no schedule
        if (! $schedule) {
            return;
        }

        $options = $schedule->options ?? [];

        // Only reset if max_retries is explicitly set and greater than zero
        $maxRetries = isset($options['max_retries']) ? (int) $options['max_retries'] : 0;
        if ($maxRetries <= 0) {
            return;
        }

        // Reset failed_runs back to zero
        $schedule->update(['failed_runs' => 0]);
    }
}
