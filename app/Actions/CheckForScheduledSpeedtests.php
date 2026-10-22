<?php

namespace App\Actions;

use App\Actions\Speedtests\RunSpeedtest;
use App\Enums\ResultService;
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
            $this->isSpeedtestDue(schedule: $schedule),
            service: ResultService::tryFrom((string) config('speedtest.service')) ?? ResultService::Ookla,
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
}
