<?php

namespace App\Actions;

use App\Actions\Ookla\RunSpeedtest;
use App\Models\Schedule;
use Cron\CronExpression;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckForScheduledSpeedtests
{
    use AsAction;

    public function handle(): void
    {
        // Get all active schedules
        $activeSchedules = Schedule::where('is_active', true)->get();

        foreach ($activeSchedules as $schedule) {
            $expression = data_get($schedule, 'options.cron_expression');

            if (is_string($expression) && $this->isSpeedtestDue($expression)) {
                $serverPreference = data_get($schedule->options, 'server_preference', 'auto');
                $servers = data_get($schedule->options, 'servers', []);
                $skipIps = data_get($schedule->options, 'skip_ips', []);
                $interface = data_get($schedule->options, 'interface');

                RunSpeedtest::dispatch(
                    scheduled: true,
                    schedule: $schedule,
                    scheduleOptions: [
                        'server_preference' => $serverPreference,
                        'servers' => $servers,
                        'skip_ips' => $skipIps,
                        'interface' => $interface,
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
