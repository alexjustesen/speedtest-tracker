<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Cron\CronExpression;

class ScheduledSpeedtestService
{
    /**
     * Assess if there are scheduled speedtests and return the next scheduled time.
     *
     * @return Carbon|null Returns null if no tests are scheduled, or Carbon instance with next scheduled test
     */
    public static function getNextScheduledTest(): ?Carbon
    {
        $schedule = config('speedtest.schedule');

        if (blank($schedule) || $schedule === false) {
            return null;
        }

        $cronExpression = new CronExpression($schedule);

        return Carbon::parse(
            time: $cronExpression->getNextRunDate(timeZone: config('app.display_timezone'))
        );
    }

    public static function getScheduleInterval(): ?CarbonInterval
    {
        $schedule = config('speedtest.schedule');

        if (blank($schedule) || $schedule === false) {
            return null;
        }

        $cronExpression = new CronExpression($schedule);

        $prev = Carbon::parse(
            time: $cronExpression->getPreviousRunDate(timeZone: config('app.display_timezone'))
        );
        $next = Carbon::parse(
            time: $cronExpression->getNextRunDate(timeZone: config('app.display_timezone'))
        );

        return $prev->diff($next, true);
    }
}
