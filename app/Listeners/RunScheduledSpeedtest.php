<?php

namespace App\Listeners;

use App\Actions\Schedules\RunScheduledSpeedtest as RunScheduledSpeedtestAction;
use App\Models\Speedtest;
use DirectoryTree\Cadence\Events\ScheduleTriggered;

class RunScheduledSpeedtest
{
    /**
     * Handle the event.
     */
    public function handle(ScheduleTriggered $event): void
    {
        $schedulable = $event->schedule->schedulable;

        if (! $schedulable instanceof Speedtest) {
            return;
        }

        RunScheduledSpeedtestAction::run($schedulable);
    }
}
