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

        $cronExpression = $schedule->options['cron_expression'];
        $scheduleId = $schedule->id;

        // Track overlapping schedules
        $overlappingSchedules = [];

        // Find other active schedules that have cron expressions
        $existingCrons = Schedule::query()
            ->where('is_active', true)
            ->where('id', '!=', $scheduleId)  // Exclude the modified schedule
            ->get(['id', 'options']) // Get schedule ID and cron expression
            ->pluck('options.cron_expression', 'id'); // Map cron expression by schedule ID

        // Check for overlaps with the modified schedule's cron expression
        foreach ($existingCrons as $existingScheduleId => $existingCron) {
            if (Cron::hasOverlap($existingCron, $cronExpression)) {
                $overlappingSchedules[] = $existingScheduleId; // Store overlapping schedule IDs
            }
        }

        // Send a notification if overlaps exist
        if (count($overlappingSchedules) > 0) {
            // Generate a list of overlapping schedule IDs (or names if you prefer)
            $overlapList = implode(', ', $overlappingSchedules);

            // Send a single notification for all overlaps
            Notification::make()
                ->title('Schedule Overlap Detected')
                ->warning()
                ->body("The cron expression for this schedule overlaps with the following active schedules: $overlapList.")
                ->send();
        }
    }
}
