<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LatencySettings extends Settings
{
    public int $ping_count;

    public array $target_url;

    public string $latency_schedule = ''; // Default cron expression

    public bool $latency_enabled = false; // Default state for the enable/disable toggle

    public int|string|array $latency_column_span = 'full'; // Add this setting for columnSpan

    /**
     * Define the group name for these settings.
     */
    public static function group(): string
    {
        return 'latency';
    }
}
