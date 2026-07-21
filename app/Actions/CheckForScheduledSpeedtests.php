<?php

namespace App\Actions;

use App\Actions\Ookla\RunSpeedtest;
use Cron\CronExpression;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckForScheduledSpeedtests
{
    use AsAction;

    public function handle(): void
    {
        $schedule = config('speedtest.schedule');

        RunSpeedtest::runIf(
            $this->isSpeedtestDue(schedule: $schedule),
            scheduled: true,
        );
    }

    /**
     * Assess if a speedtest is due to run based on the schedule.
     */
    private function isSpeedtestDue(Collection $schedule): bool
    {
        return $schedule->map(fn ($expression) => new CronExpression($expression))
            ->filter(fn ($cron) => $cron->isDue(currentTime: now(), timeZone: config('app.display_timezone')))
            ->isNotEmpty();
    }
}
