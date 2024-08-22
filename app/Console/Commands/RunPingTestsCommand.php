<?php

namespace App\Actions\Pings;

use App\Jobs\Pings\ExecutePingTest;
use Cron\CronExpression;
use Lorisleiva\Actions\Concerns\AsAction;

class RunScheduledPingTests
{
    use AsAction;

    public function handle(): void
    {
        $cronExpression = new CronExpression(config('ping.schedule'));

        if (! $cronExpression->isDue(now()->timezone(config('app.display_timezone')))) {
            return;
        }

        $urls = config('ping.urls');

        foreach ($urls as $url) {
            ExecutePingTest::dispatch($url);
        }
    }
}
