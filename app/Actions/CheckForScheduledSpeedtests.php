<?php

namespace App\Actions;

use App\Actions\Ookla\RunSpeedtest;
use Cron\CronExpression;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckForScheduledSpeedtests
{
    use AsAction;

    public function handle(): void
    {
        $schedule = config('speedtest.schedule');

        if (blank($schedule) || $schedule === false) {
            return;
        }

        RunSpeedtest::runIf(
            $this->isSpeedtestDue(schedule: $schedule) && ! $this->isSpeedtestIsPaused(),
            scheduled: true,
        );
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

    private function isSpeedtestIsPaused(): bool
    {
        $paseDate = config('speedtest.schedule_paused_until');

        return ! blank($paseDate) && Carbon::createFromFormat('Y-m-d H:i:s', $paseDate)->isBefore(now());
    }
}
