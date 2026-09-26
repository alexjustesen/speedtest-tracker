<?php

namespace App\Services;

use App\Models\Speedtest;
use Carbon\Carbon;
use DirectoryTree\Cadence\Schedule as CadenceSchedule;

class ScheduledSpeedtestService
{
    /**
     * Assess if there are scheduled speedtests and return the next scheduled time.
     *
     * @return Carbon|null Returns null if no schedules are enabled, or Carbon instance with the soonest next run
     */
    public static function getNextScheduledTest(): ?Carbon
    {
        return CadenceSchedule::query()
            ->enabled()
            ->where('schedulable_type', Speedtest::class)
            ->whereNotNull('next_run_at')
            ->orderBy('next_run_at')
            ->first()
            ?->next_run_at;
    }
}
