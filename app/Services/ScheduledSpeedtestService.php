<?php

namespace App\Services;

use Carbon\Carbon;
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
}
