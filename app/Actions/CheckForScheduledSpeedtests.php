<?php

namespace App\Actions;

use App\Actions\Speedtests\RunSpeedtest;
use App\Services\ScheduledSpeedtestService;
use Cron\CronExpression;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckForScheduledSpeedtests
{
    use AsAction;

    public function handle(): void
    {
        foreach (ScheduledSpeedtestService::schedules() as $schedule) {
            RunSpeedtest::runIf(
                $this->isSpeedtestDue(schedule: $schedule['cron']),
                service: $schedule['service'],
                scheduled: true,
                target: $schedule['target'],
            );
        }
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
