<?php

namespace App\Listeners;

use App\Events\SpeedtestBenchmarkFailed;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Jobs\Ookla\RetrySpeedtestWithDifferentServer;
use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Illuminate\Events\Dispatcher;

class SpeedtestEventSubscriber
{
    /**
     * Handle speedtest failed events.
     */
    public function handleSpeedtestFailed(SpeedtestFailed $event): void
    {
        if (! $this->shouldRetry($event->result, 'speedtest')) {
            return;
        }

        RetrySpeedtestWithDifferentServer::dispatch($event->result);
    }

    /**
     * Handle speedtest completed events.
     */
    public function handleSpeedtestCompleted(SpeedtestCompleted $event): void
    {
        $settings = app(DataIntegrationSettings::class);

        if ($settings->influxdb_v2_enabled) {
            WriteResult::dispatch($event->result);
        }
    }

    public function handleSpeedtestBenchmarkFailed(SpeedtestBenchmarkFailed $event): void
    {
        if (! $this->shouldRetry($event->result, 'benchmark')) {
            return;
        }

        RetrySpeedtestWithDifferentServer::dispatch($event->result);
    }

    protected function shouldRetry(Result $result, string $type): bool
    {
        $schedule = $result->schedule;
        if (! $schedule) {
            return false;
        }

        $retries = $schedule->retries ?? [];

        if (empty($retries['enabled'])) {
            return false;
        }

        // Check the specific retry type
        if (
            ($type === 'speedtest' && empty($retries['speedtest_enabled'])) ||
            ($type === 'benchmark' && empty($retries['benchmark_enabled']))
        ) {
            return false;
        }

        // Check retry limit
        $maxRetries = (int) ($retries['max_retries'] ?? 0);
        if ($maxRetries < 1 || $schedule->failed_runs >= ($maxRetries + 1)) {
            return false;
        }

        return true;
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            SpeedtestFailed::class,
            [SpeedtestEventSubscriber::class, 'handleSpeedtestFailed']
        );

        $events->listen(
            SpeedtestCompleted::class,
            [SpeedtestEventSubscriber::class, 'handleSpeedtestCompleted']
        );

        $events->listen(
            SpeedtestBenchmarkFailed::class,
            [SpeedtestEventSubscriber::class, 'handleSpeedtestBenchmarkFailed']
        );
    }
}
