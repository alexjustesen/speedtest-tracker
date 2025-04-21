<?php

namespace App\Observers;

use App\Actions\CheckCronOverlap;
use App\Actions\UpdateNextRun;
use App\Models\Schedule;
use Illuminate\Support\Str;

class ScheduleObserver
{
    /**
     * Handle the Schedule "creating" event.
     */
    public function creating(Schedule $schedule): void
    {
        do {
            $token = Str::lower(Str::random(16));
        } while (Schedule::where('token', $token)->exists());

        $schedule->token = $token;
    }

    /**
     * Handle the Schedule "created" event.
     */
    public function created(Schedule $schedule): void
    {
        UpdateNextRun::run();
    }

    /**
     * Handle the Schedule "updated" event.
     */
    public function updated(Schedule $schedule): void
    {
        UpdateNextRun::run();
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
