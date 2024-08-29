<?php

namespace App\Actions\Latency;

use App\Jobs\Latency\ExecuteLatencyTest;
use Cron\CronExpression;
use Lorisleiva\Actions\Concerns\AsAction;

class RunScheduledLatencyTests
{
    use AsAction;

    public function handle(): void
    {
        $cronExpression = new CronExpression(config('latency.schedule'));

        if (! $cronExpression->isDue(now()->timezone(config('app.display_timezone')))) {
            return;
        }

        $urls = config('latency.urls');

        foreach ($urls as $url) {
            ExecuteLatencyTest::dispatch($url);
        }
    }
}
