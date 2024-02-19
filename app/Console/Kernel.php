<?php

namespace App\Console;

use App\Console\Commands\RunOoklaSpeedtest;
use App\Console\Commands\SystemMaintenance;
use App\Console\Commands\VersionChecker;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Cron\CronExpression;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $settings = new GeneralSettings();

        /**
         * Checks if Result model records should be pruned.
         */
        if ($settings->prune_results_older_than > 0) {
            $schedule->command('model:prune', [
                '--model' => [Result::class],
            ])->daily();
        }

        /**
         * Perform system maintenance weekly on Sunday morning,
         * start off the week nice and fresh.
         */
        $schedule->command(SystemMaintenance::class)->weeklyOn(0)
            ->timezone($settings->timezone ?? 'UTC');

        /**
         * Checked for new versions weekly on Thursday because
         * I usually do releases on Thursday or Friday.
         */
        $schedule->command(VersionChecker::class)->weeklyOn(5)
            ->timezone($settings->timezone ?? 'UTC');

        /**
         * Check if an Ookla Speedtest needs to run.
         */
        $schedule->command(RunOoklaSpeedtest::class, ['--scheduled'])->everyMinute()
            ->timezone($settings->timezone ?? 'UTC')
            ->when(function () use ($settings) {
                if (blank($settings->speedtest_schedule)) {
                    return false;
                }

                return (new CronExpression($settings->speedtest_schedule))
                    ->isDue(now()->timezone($settings->timezone ?? 'UTC'));
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
