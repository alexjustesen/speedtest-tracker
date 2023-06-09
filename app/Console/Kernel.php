<?php

namespace App\Console;

use App\Jobs\ExecSpeedtest;
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
         * Check for speedtests that need to run.
         */
        $schedule->call(function () use ($settings) {
            $ooklaServerId = null;

            if (! blank($settings->speedtest_server)) {
                $item = array_rand($settings->speedtest_server);

                $ooklaServerId = $settings->speedtest_server[$item];
            }

            $speedtest = [
                'ookla_server_id' => $ooklaServerId,
            ];

            ExecSpeedtest::dispatch(
                speedtest: $speedtest,
                scheduled: true
            );
        })
            ->everyMinute()
            ->when(function () use ($settings) {
                // Don't run if the schedule is missing (aka disabled)
                if (blank($settings->speedtest_schedule)) {
                    return false;
                }

                // Evaluate if a run is needed based on the schedule
                $cron = new CronExpression($settings->speedtest_schedule);

                return $cron->isDue();
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
