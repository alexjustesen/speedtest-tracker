<?php

namespace App\Actions;

use App\Models\Schedule;
use Carbon\Carbon;
use Cron\CronExpression;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNextRun
{
    use AsAction;

    public function handle(): void
    {
        // Disable model events to prevent triggering 'updated' event during the save
        Schedule::withoutEvents(function () {
            // Fetch all schedules that need to be updated
            $schedules = Schedule::all();

            foreach ($schedules as $schedule) {
                // Get the cron expression from the schedule options
                $expression = data_get($schedule, 'options.cron_expression');

                if ($expression) {
                    // Calculate the next run time based on the cron expression
                    $nextRun = $this->getNextRunAt($expression);

                    // Update the schedule with the next run time
                    $schedule->next_run_at = $nextRun;
                    $schedule->save();
                }
            }
        });
    }

    /**
     * Calculate the next run time from a cron expression.
     */
    private function getNextRunAt(string $expression): Carbon
    {
        // Create a CronExpression instance from the cron expression
        $cron = CronExpression::factory($expression);

        // Get the next valid run time
        $nextRun = $cron->getNextRunDate();

        // Return the next run time as a Carbon instance
        return Carbon::parse($nextRun);
    }
}
