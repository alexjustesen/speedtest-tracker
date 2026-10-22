<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SpeedtestSettings extends Settings
{
    /**
     * Service preselected in the Speedtest dialog.
     */
    public string $default_service;

    /**
     * Scheduled speedtests: list of ['enabled' => bool, 'service' => string, 'cron' => string, 'target' => ?string].
     */
    public array $schedules;

    /**
     * iPerf3 servers as "host" or "host:port".
     */
    public array $iperf3_servers;

    public array $file_urls;

    public int $http_duration;

    public int $http_streams;

    public int $iperf3_duration;

    public int $iperf3_streams;

    public static function group(): string
    {
        return 'speedtest';
    }
}
