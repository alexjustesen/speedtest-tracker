<?php

namespace App\Actions\LatencyTests;

use App\Jobs\Latency\ExecuteLatencyTest;
use Cron\CronExpression;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Settings\LatencySettings;

class RunScheduledLatencyTests
{
    use AsAction;

    public function handle(): void
    {
        $settings = $this->getSettings();

        // Check if latency tests are enabled
        if (!$settings->latency_enabled) {
            Log::info('Latency tests are disabled in the settings. Exiting.');
            return;
        }

        // Proceed with scheduling checks if enabled
        $cronExpression = new CronExpression($settings->latency_schedule);
        $now = now()->timezone(config('app.display_timezone'));

        Log::info('Checking if latency test is due.', [
            'current_time' => $now,
            'is_due' => $cronExpression->isDue($now),
        ]);

        if (!$cronExpression->isDue($now)) {
            Log::info('Latency test is not due. Exiting.');
            return;
        }

        $urls = $settings->target_url;
        Log::info('Running latency tests for URLs', ['urls' => $urls]);

        foreach ($urls as $urlItem) {
            if (is_array($urlItem) && isset($urlItem['url']) && is_string($urlItem['url'])) {
                $url = trim($urlItem['url']);
                $target_name = $urlItem['target_name'] ?? 'Unnamed'; // Default to 'Unnamed' if no name is provided
                if ($url) {
                    Log::info('Dispatching latency test', ['url' => $url, 'name' => $target_name]);
                    ExecuteLatencyTest::dispatch($url, $target_name);
                }
            } else {
                Log::warning('Skipping invalid URL entry', ['urlItem' => $urlItem]);
            }
        }
    }

    protected function getSettings(): LatencySettings
    {
        return app(LatencySettings::class);
    }
}
