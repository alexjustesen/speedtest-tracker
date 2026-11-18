<?php

namespace App\Schedules;

use Carbon\CarbonInterface;
use DirectoryTree\Cadence\Drivers\CronSchedule as BaseCronSchedule;

/**
 * Cadence resolves occurrences in the schedule's timezone, but Eloquent stores
 * datetimes as wall-clock strings without conversion. Normalize occurrences to
 * the app timezone so "next_run_at" compares correctly against now().
 */
class CronSchedule extends BaseCronSchedule
{
    public function getNextOccurrence(CarbonInterface $after): ?CarbonInterface
    {
        return parent::getNextOccurrence($after)?->setTimezone(config('app.timezone'));
    }
}
