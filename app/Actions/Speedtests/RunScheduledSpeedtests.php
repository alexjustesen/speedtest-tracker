<?php

namespace App\Actions\Speedtests;

use App\Settings\GeneralSettings;
use Cron\CronExpression;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

class RunScheduledSpeedtests
{
    use AsAction;

    public function handle()
    {
        $settings = new GeneralSettings();

        /**
         * Ookla speedtests.
         */
        $cronExpression = new CronExpression($settings->speedtest_schedule);

        if ($cronExpression->isDue(now()->timezone($settings->timezone ?? 'UTC'))) {
            $serverId = null;

            if (is_array($settings->speedtest_server) && count($settings->speedtest_server)) {
                $serverId = Arr::random($settings->speedtest_server);
            }

            RunOoklaSpeedtest::run(serverId: $serverId, scheduled: true);
        }
    }
}
