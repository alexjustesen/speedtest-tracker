<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LatencySettings extends Settings
{
    public int $ping_count;
    public array $ping_urls;
    public string $cron_expression;

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
