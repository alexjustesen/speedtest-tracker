<?php

namespace App\Actions;

use App\Models\Schedule;
use Carbon\Carbon;
use Cron\CronExpression;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Actions\Ookla\StartSpeedtest;
use Illuminate\Support\Arr;

class CheckForScheduledSpeedtests
{
    use AsAction;

    public function handle(): void
    {
        // Get all active schedules
        $activeSchedules = Schedule::where('is_active', true)->get();

        foreach ($activeSchedules as $schedule) {
            // Get the cron expression for the schedule
            $expression = data_get($schedule, 'options.cron_expression');

            // If there's a cron expression and the speedtest is due, dispatch the job
            if (is_string($expression) && $this->isSpeedtestDue($expression)) {
                // Fetch the server preferences and other options
                $serverPreference = data_get($schedule->options, 'server_preference', 'auto');
                $preference = Arr::get($this->scheduleOptions, 'server_preference', 'auto');
                $preferredServers = Arr::get($this->scheduleOptions, 'servers', []);

                StartSpeedtest::dispatch(
                    scheduled: true,
                    schedule: $schedule,
                    scheduleOptions: [
                        'server_preference' => $serverPreference,
                        'servers' => $servers,
                        'skip_ips' => $skipIps
                    ]
                );
            }
        }
    }

    // Check if the speedtest is due based on the cron expression
    private function isSpeedtestDue(string $expression): bool
    {
        $cron = new CronExpression($expression);
        return $cron->isDue(
            currentTime: now(),
            timeZone: config('app.display_timezone')
        );
    }
}
