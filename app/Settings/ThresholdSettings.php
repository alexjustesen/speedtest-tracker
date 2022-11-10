<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ThresholdSettings extends Settings
{
    public bool $absolute_enabled;

    public ?float $absolute_download;

    public ?float $absolute_upload;

    public ?float $absolute_ping;

    public static function group(): string
    {
        return 'threshold';
    }
}
