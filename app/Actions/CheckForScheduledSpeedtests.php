<?php
namespace App\Actions;

use App\Models\Test;
use Cron\CronExpression;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Actions\Ookla\StartSpeedtest;
use Illuminate\Support\Arr;

class CheckForScheduledSpeedtests
{
    use AsAction;

    public function handle(): void
    {
        $activeTests = Test::where('is_active', true)->get();

        foreach ($activeTests as $test) {
            $expression = data_get($test, 'options.cron_expression');
            
            // If there's a single cron expression (legacy format)
            if (is_string($expression) && $this->isSpeedtestDue($expression)) {
                // Run with legacy format
                StartSpeedtest::dispatch(
                    scheduled: true,
                    test: $test
                );
                
                // Update next run for single cron
                $test->update([
                    'next_run_at' => $this->getNextRunAt($expression),
                ]);
                continue;
            }
            
            // Handle multiple schedules (if expression is an array of schedules)
            // Each schedule can have its own cron expression and server preferences
            $schedules = data_get($test, 'options.schedules', []);
            
            foreach ($schedules as $index => $schedule) {
                $scheduleExpression = data_get($schedule, 'cron_expression');
                
                if (!$scheduleExpression || !$this->isSpeedtestDue($scheduleExpression)) {
                    continue;
                }
                
                // Get schedule-specific server preferences
                $serverPreference = data_get($schedule, 'server_preference', 'auto');
                $servers = data_get($schedule, 'servers', []);
                
                // Run the speedtest with schedule-specific options
                StartSpeedtest::dispatch(
                    scheduled: true,
                    test: $test,
                    scheduleOptions: [
                        'schedule_index' => $index,
                        'server_preference' => $serverPreference,
                        'servers' => $servers,
                        'skip_ips' => data_get($schedule, 'skip_ips', data_get($test, 'options.skip_ips', []))
                    ]
                );
                
                // Update the next run time for this specific schedule
                $nextRunTimes = data_get($test, 'options.next_run_times', []);
                $nextRunTimes[$index] = $this->getNextRunAt($scheduleExpression)->toIso8601String();
                
                $test->update([
                    'options->next_run_times' => $nextRunTimes,
                    // Update the main next_run_at to the earliest of all schedules
                    'next_run_at' => $this->calculateEarliestNextRun($test, $nextRunTimes),
                ]);
            }
        }
    }

    private function isSpeedtestDue(string $expression): bool
    {
        $cron = new CronExpression($expression);
        return $cron->isDue(
            currentTime: now(),
            timeZone: config('app.display_timezone')
        );
    }

    private function getNextRunAt(string $expression): \Carbon\Carbon
    {
        $cron = new CronExpression($expression);
        return \Carbon\Carbon::instance(
            $cron->getNextRunDate(now(), 0, false, config('app.display_timezone'))
        );
    }
    
    private function calculateEarliestNextRun(Test $test, array $nextRunTimes): \Carbon\Carbon
    {
        // If no schedules with next run times, use legacy approach
        if (empty($nextRunTimes)) {
            $expression = data_get($test, 'options.cron_expression');
            if ($expression) {
                return $this->getNextRunAt($expression);
            }
            return now()->addHour(); // Fallback
        }
        
        // Find the earliest next run time among all schedules
        $earliest = null;
        foreach ($nextRunTimes as $timestamp) {
            $runTime = \Carbon\Carbon::parse($timestamp);
            if ($earliest === null || $runTime->lt($earliest)) {
                $earliest = $runTime;
            }
        }
        
        return $earliest ?? now()->addHour();
    }
}