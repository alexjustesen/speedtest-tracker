<?php

namespace App\Services;

use App\Models\Schedule;
use Carbon\Carbon;
use Cron\CronExpression;

class ScheduledSpeedtestService
{
    /**
     * Assess if there are scheduled speedtests and return the next scheduled time.
     *
     * @return Carbon|null Returns null if no schedules are enabled, or Carbon instance with the soonest next run
     */
    public static function getNextScheduledTest(): ?Carbon
    {
        return Schedule::query()
            ->where('enabled', true)
            ->get()
            ->map(function (Schedule $schedule) {
                $cron = new CronExpression($schedule->schedule);

                return Carbon::parse(
                    time: $cron->getNextRunDate(timeZone: config('app.display_timezone'))
                );
            })
            ->sort()
            ->first();
    }
}
