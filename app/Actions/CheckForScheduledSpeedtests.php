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
        Schedule::query()->where('enabled', true)->each(function (Schedule $schedule) {
            if (! $this->isSpeedtestDue($schedule->schedule)) {
                return;
            }

            RunSpeedtest::run(scheduled: true, schedule: $schedule);
        });
    }

    /**
     * Assess if a speedtest is due to run based on the schedule.
     */
    private function isSpeedtestDue(string $schedule): bool
    {
        $cron = new CronExpression($schedule);

        return $cron->isDue(
            currentTime: now(),
            timeZone: config('app.display_timezone')
        );
    }
}
