<?php

namespace App\Actions;

use App\Helpers\Cron;
use App\Models\Schedule;
use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckCronOverlap
{
    use AsAction;

    public static function run(Schedule $schedule): void
    {

        // Ensure schedule has a cron expression and is active
        if (! $schedule->is_active) {

            return;
        }

        $cronExpression = $schedule->schedule;
        $scheduleId = $schedule->id;

        // Track overlapping schedules
        $overlappingSchedules = [];

        // Find other active schedules that have cron expressions
        $existingCrons = Schedule::query()
            ->where('is_active', true)
            ->where('id', '!=', $scheduleId)
            ->get(['id', 'schedule'])
            ->pluck('schedule', 'id');

        // Check for overlaps with the modified schedule's cron expression
        foreach ($existingCrons as $existingScheduleId => $existingCron) {
            if (Cron::hasOverlap($existingCron, $cronExpression)) {
                $overlappingSchedules[] = $existingScheduleId;
            }
        }

        // Send a notification if overlaps exist
        if (count($overlappingSchedules) > 0) {
            $overlapList = implode(', ', $overlappingSchedules);

            // Send a single notification for all overlaps
            Notification::make()
                ->title(__('schedules.overlap_detected'))
                ->warning()
                ->body(__('schedules.overlap_body', ['ids' => $overlapList]))
                ->send();
        }
    }
}
