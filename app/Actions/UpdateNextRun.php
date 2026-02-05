<?php

namespace App\Actions;

use App\Models\Schedule;
use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Contracts\Queue\ShouldQueue;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNextRun implements ShouldQueue
{
    use AsAction;

    public function handle(Schedule $schedule): void
    {
        // Disable model events for the entire action
        Schedule::withoutEvents(function () use ($schedule) {

            // If the schedule is not active, clear the next_run_at field
            if (! $schedule->is_active) {
                if ($schedule->next_run_at !== null) {
                    // Update without firing events
                    $schedule->next_run_at = null;
                    $schedule->save();
                }

                return;
            }

            // Get the cron expression from the schedule column
            $expression = $schedule->schedule;

            if ($expression) {
                // Calculate the next run time based on the cron expression
                $nextRun = $this->getNextRunAt($expression);

                // Only update if the next_run_at field is different from the calculated next run time
                if ($schedule->next_run_at !== $nextRun) {
                    $schedule->next_run_at = $nextRun;
                    $schedule->save();
                }
            }

        });  // End of withoutEvents closure
    }

    // Calculate the next run time based on the cron expression
    private function getNextRunAt(string $expression): Carbon
    {
        // Create a CronExpression instance from the cron expression
        $cron = CronExpression::factory($expression);

        // Get the next valid run time
        return Carbon::parse($cron->getNextRunDate());
    }
}
