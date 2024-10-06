<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MetricsSettings extends Settings
{
    public bool $prometheus_enabled;

    public bool $influxdb_v2_enabled;

    public ?string $influxdb_v2_url;

    public ?string $influxdb_v2_org;

    public string $influxdb_v2_bucket;

    public ?string $influxdb_v2_token;

    public bool $influxdb_v2_verify_ssl;

    public static function group(): string
    {
        return 'metrics';
    }
}