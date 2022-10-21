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
        $speedtest = [
            'enabled' => ! blank($settings->speedtest_schedule),
            'schedule' => optional($settings)->speedtest_schedule,
            'ookla_server_id' => optional($settings)->speedtest_server,
        ];

        if ($speedtest['enabled']) {
            $cron = new CronExpression($speedtest['schedule']);

            if ($cron->isDue()) {
                ExecSpeedtest::dispatch(speedtest: $speedtest, scheduled: true);
            }
        }
    }
}
