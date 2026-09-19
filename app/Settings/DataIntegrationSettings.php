<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DataIntegrationSettings extends Settings
{
    public bool $influxdb_v2_enabled;

    public ?string $influxdb_v2_url;

    public ?string $influxdb_v2_org;

    public string $influxdb_v2_bucket;

    public ?string $influxdb_v2_token;

    public bool $influxdb_v2_verify_ssl;

    public bool $prometheus_enabled;

    public array $prometheus_allowed_ips = [];

    public bool $prometheus_remote_write_enabled;

    public ?string $prometheus_remote_write_url;

    public ?string $prometheus_remote_write_username;

    public ?string $prometheus_remote_write_password;

    public static function group(): string
    {
        return 'dataintegration';
    }
}
