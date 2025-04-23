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
        $result = $event->result;
        $schedule = $result->schedule;

        if (! $schedule) {
            return;
        }

        $options = $schedule->options ?? [];

        $maxRetries = (int) ($options['max_retries'] ?? 0);
        if ($maxRetries <= 0 || $schedule->failed_runs <= 0) {
            return;
        }

        $retrySpeedtest = ! empty($options['retries_speedtest_enabled']);
        $retryBenchmark = ! empty($options['retries_benchmark_enabled']);

        $speedtestHealthy = $result->type === 'speedtest' && $result->healthy;
        $benchmarkHealthy = $result->type === 'benchmark' && $result->healthy;

        $bothRetryTypesEnabled = $retrySpeedtest && $retryBenchmark;

        if (
            ($bothRetryTypesEnabled && $speedtestHealthy && $benchmarkHealthy) ||
            ($retrySpeedtest && ! $retryBenchmark && $speedtestHealthy) ||
            ($retryBenchmark && ! $retrySpeedtest && $benchmarkHealthy)
        ) {
            $schedule->update(['failed_runs' => 0]);

            logger()->info("Reset failed_runs for schedule #{$schedule->id} after a healthy {$result->type} result (#{$result->id}).");
        }
    }
}
