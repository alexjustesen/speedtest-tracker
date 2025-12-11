<?php

namespace App\Observers;

use App\Actions\CheckCronOverlap;
use App\Actions\UpdateNextRun;
use App\Models\Schedule;

class ScheduleObserver
{
    /**
     * Handle the Schedule "creating" event.
     */
    public function creating(Schedule $schedule): void
    {
        //
    }

    /**
     * Handle the Schedule "created" event.
     */
    public function created(Schedule $schedule): void
    {
        UpdateNextRun::run($schedule);
        CheckCronOverlap::run($schedule);
    }

    /**
     * Handle the Schedule "updating" event.
     */
    public function updating(Schedule $schedule): void
    {
        if ($schedule->isDirty('cron_schedule')) {
            UpdateNextRun::run($schedule);
            CheckCronOverlap::run($schedule);
        }
    }

    /**
     * Handle the Schedule "updated" event.
     */
    public function updated(Schedule $schedule): void
    {
        UpdateNextRun::run($schedule);
        CheckCronOverlap::run($schedule);
    }

    /**
     * Handle the Schedule "deleted" event.
     */
    public function deleted(Schedule $schedule): void
    {
        //
    }

    /**
     * Handle the Schedule "restored" event.
     */
    public function restored(Schedule $schedule): void
    {
        //
    }

    /**
     * Handle the Schedule "force deleted" event.
     */
    public function forceDeleted(Schedule $schedule): void
    {
        //
    }
}
