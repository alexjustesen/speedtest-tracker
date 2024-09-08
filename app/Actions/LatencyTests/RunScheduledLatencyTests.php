<?php

namespace App\Actions\LatencyTests;

use App\Jobs\Latency\ExecuteLatencyTest;
use App\Settings\LatencySettings;
use Cron\CronExpression;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class RunScheduledLatencyTests
{
    use AsAction;

    public function handle(): void
    {
        $settings = $this->getSettings();

        // Check if latency tests are enabled
        if (! $settings->latency_enabled) {
            return;
        }

        // Proceed with scheduling checks if enabled
        $cronExpression = new CronExpression($settings->latency_schedule);
        $now = now()->timezone(config('app.display_timezone'));

        Log::info('Checking if latency test is due.', [
            'current_time' => $now,
            'is_due' => $cronExpression->isDue($now),
        ]);

        if (! $cronExpression->isDue($now)) {
            Log::info('Latency test is not due. Exiting.');

            return;
        }

        $urls = collect($settings->target_url)
            ->filter(fn ($urlItem) => is_array($urlItem) && isset($urlItem['url']) && is_string($urlItem['url']))
            ->map(fn ($urlItem) => trim($urlItem['url']))
            ->filter() // Remove empty URLs
            ->toArray();

        if (empty($urls)) {
            Log::warning('No valid URLs to ping.');

            return;
        }

        Log::info('Dispatching latency test for all URLs');
        ExecuteLatencyTest::dispatch($urls); // Dispatch all URLs at once
    }

    protected function getSettings(): LatencySettings
    {
        return app(LatencySettings::class);
    }
}
