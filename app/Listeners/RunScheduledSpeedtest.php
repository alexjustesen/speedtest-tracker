<?php

namespace App\Listeners;

use App\Actions\Ookla\RunSpeedtest;
use App\Models\Speedtest;
use DirectoryTree\Cadence\Events\ScheduleTriggered;

class RunScheduledSpeedtest
{
    /**
     * Handle the event.
     */
    public function handle(ScheduleTriggered $event): void
    {
        // Check the morph type first so schedules for other models never load their parent.
        if ($event->schedule->schedulable_type !== (new Speedtest)->getMorphClass()) {
            return;
        }

        $speedtest = $event->schedule->schedulable;

        if (! $speedtest instanceof Speedtest) {
            return;
        }

        RunSpeedtest::run(scheduled: true, speedtest: $speedtest);
    }
}
