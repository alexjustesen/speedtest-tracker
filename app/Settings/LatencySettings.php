<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LatencySettings extends Settings
{
    public int $ping_count;
    public array $target_url;
    public string $latency_schedule = ''; // Default cron expression
    public bool $latency_enabled = false; // Default state for the enable/disable toggle

    /**
     * Define the group name for these settings.
     *
     * @return string
     */
    public static function group(): string
    {
        return 'latency';
    }
}
