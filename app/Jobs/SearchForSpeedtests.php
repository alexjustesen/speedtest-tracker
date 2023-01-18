<?php

namespace App\Jobs;

use App\Settings\GeneralSettings;
use Cron\CronExpression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SearchForSpeedtests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GeneralSettings $settings)
    {
        $ookla_server_id = null;

        if (! blank($settings->speedtest_server)) {
            $item = array_rand($settings->speedtest_server);

            $ookla_server_id = $settings->speedtest_server[$item];
        }

        $speedtest = [
            'enabled' => ! blank($settings->speedtest_schedule),
            'schedule' => optional($settings)->speedtest_schedule,
            'ookla_server_id' => $ookla_server_id,
        ];

        if ($speedtest['enabled']) {
            $cron = new CronExpression($speedtest['schedule']);

            if ($cron->isDue()) {
                ExecSpeedtest::dispatch(speedtest: $speedtest, scheduled: true);
            }
        }
    }
}
