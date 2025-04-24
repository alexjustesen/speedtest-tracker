<?php

namespace App\Jobs\Ookla;

use App\Actions\GetOoklaSpeedtestServers;
use App\Actions\Ookla\StartSpeedtest;
use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class RetrySpeedtestWithDifferentServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Result $failedResult;

    public function __construct(Result $failedResult)
    {
        $this->failedResult = $failedResult;
    }

    public function handle(): void
    {
        $failed = $this->failedResult;
        $schedule = $failed->schedule;

        if (! $schedule) {
            return;
        }

        // Pull retries config
        $retries = $schedule->retries ?? [];

        $enabled = $retries['enabled'] ?? false;
        $maxRetries = (int) ($retries['max_retries'] ?? 0);

        if (! $enabled || $maxRetries < 1) {
            return;
        }

        if ($schedule->failed_runs >= ($maxRetries + 1)) {
            return;
        }

        $schedule->increment('failed_runs');

        $options = $schedule->options ?? [];
        $preference = $options['server_preference'] ?? 'auto';
        $explicit = Arr::pluck($options['servers'] ?? [], 'server_id');

        $allServers = array_keys(GetOoklaSpeedtestServers::run());
        $currentId = data_get($failed->data, 'server.id');

        switch ($preference) {
            case 'prefer':
                $candidates = array_diff($explicit, [$currentId]);
                break;

            case 'ignore':
                $candidates = array_diff($allServers, $explicit, [$currentId]);
                break;

            default:
                $candidates = array_diff($allServers, [$currentId]);
        }

        if (empty($candidates)) {
            $candidates = array_diff($allServers, [$currentId]);
        }

        $newServerId = Arr::random($candidates);

        StartSpeedtest::run(
            scheduled: $failed->scheduled,
            schedule: $schedule,
            scheduleOptions: $options,
            serverId: $newServerId,
            retry: true,
        );
    }
}
