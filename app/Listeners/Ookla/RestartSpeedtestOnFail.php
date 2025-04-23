<?php

namespace App\Listeners\Ookla;

use App\Actions\GetOoklaSpeedtestServers;
use App\Actions\Ookla\StartSpeedtest;
use App\Events\SpeedtestFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;

class RestartSpeedtestOnFail implements ShouldQueue
{
    /**
     * Handle a failed speedtest and retry with a different server.
     */
    public function handle(SpeedtestFailed $event): void
    {
        $failed = $event->result;
        $schedule = $failed->schedule;

        // No schedule? Nothing to do.
        if (! $schedule) {
            return;
        }

        $options = $schedule->options ?? [];

        // Determine per-schedule retry limit (only from DB)
        // Only proceed if max_retries is greater than zero
        $limit = isset($options['max_retries']) ? (int) $options['max_retries'] : 0;
        if ($limit <= 0) {
            return;
        }

        // Bail if we've already retried too many times
        if ($schedule->failed_runs >= $limit) {
            return;
        }

        // Increment the schedule-level failure counter
        $schedule->increment('failed_runs');

        // Extract server preference and lists
        $preference = $options['server_preference'] ?? 'auto';
        $explicit = Arr::pluck($options['servers'] ?? [], 'server_id');

        // Fetch all available server IDs
        $allServers = array_keys(GetOoklaSpeedtestServers::run());
        $currentId = data_get($failed->data, 'server.id');

        // Build candidate list based on preference
        switch ($preference) {
            case 'prefer':
                $candidates = array_diff($explicit, [$currentId]);
                break;

            case 'ignore':
                $candidates = array_diff($allServers, $explicit, [$currentId]);
                break;

            default: // 'auto'
                $candidates = array_diff($allServers, [$currentId]);
        }

        // Fallback: if no candidates remain, use all but the failed one
        if (empty($candidates)) {
            $candidates = array_diff($allServers, [$currentId]);
        }

        // Choose a new server at random
        $newServerId = Arr::random($candidates);

        // Dispatch a new speedtest run
        StartSpeedtest::run(
            scheduled: $failed->scheduled,
            schedule: $schedule,
            scheduleOptions: $options,
            serverId: $newServerId,
        );
    }
}
