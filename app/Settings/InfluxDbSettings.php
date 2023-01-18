<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InfluxDbSettings extends Settings
{
    public bool $v2_enabled;

    public ?string $v2_url;

    public ?string $v2_org;

    public ?string $v2_bucket;

    public ?string $v2_token;

    public static function group(): string
    {
        return 'influxdb';
    }
}
