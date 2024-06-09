<?php

namespace App\Actions\Speedtests;

use Cron\CronExpression;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

class RunScheduledSpeedtests
{
    use AsAction;

    public function handle(): void
    {
        $cronExpression = new CronExpression(config('speedtest.schedule'));

        if (! $cronExpression->isDue(now()->timezone(config('app.display_timezone')))) {
            return;
        }

        $servers = array_filter(
            explode(',', config('speedtest.servers'))
        );

        $serverId = null;

        if (count($servers)) {
            $serverId = Arr::random($servers);
        }

        RunOoklaSpeedtest::run(
            serverId: $serverId,
            scheduled: true,
        );
    }
}
