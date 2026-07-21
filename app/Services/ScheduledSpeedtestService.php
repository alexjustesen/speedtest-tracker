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

        return $schedule
            ->map(fn ($expression) => Carbon::parse(time: (new CronExpression($expression))->getNextRunDate(timeZone: config('app.display_timezone'))))
            ->sort()
            ->first();
    }
}
