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
        if (! $schedule->is_active || ! isset($schedule->options['cron_expression'])) {
            return;
        }

        $cronExpression = $schedule->options['cron_expression'];
        $scheduleId = $schedule->id;

        // Track whether we found an overlap
        $hasOverlap = false;

        $existingCrons = Schedule::query()
            ->where('is_active', true)
            ->where('id', '!=', $scheduleId)
            ->get()
            ->pluck('options')
            ->filter(fn ($options) => isset($options['cron_expression']))
            ->pluck('cron_expression');

        foreach ($existingCrons as $existingCron) {
            if (Cron::hasOverlap($existingCron, $cronExpression)) {
                $hasOverlap = true;
                break;
            }
        }

        if ($hasOverlap) {
            Notification::make()
                ->title('Schedule Overlap Detected')
                ->warning()
                ->body('The cron expression for this schedule overlaps with another active schedule.')
                ->send();
        }
    }
}
