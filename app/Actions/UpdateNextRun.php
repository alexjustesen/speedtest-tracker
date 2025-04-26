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
        if (! $schedule->is_active) {
            // If the schedule is not active, clear the next_run_at
            if ($schedule->next_run_at !== null) {
                $schedule->next_run_at = null;
                $schedule->save();
            }

            return;
        }

        $expression = data_get($schedule, 'options.cron_expression');

        if ($expression) {
            $nextRun = $this->getNextRunAt($expression);

            if (! $schedule->next_run_at || ! $schedule->next_run_at->equalTo($nextRun)) {
                $schedule->next_run_at = $nextRun;
                $schedule->save();
            }
        }
    }

    private function getNextRunAt(string $expression): Carbon
    {
        $cron = CronExpression::factory($expression);
        $nextRun = $cron->getNextRunDate();

        return Carbon::parse($nextRun);
    }
}
