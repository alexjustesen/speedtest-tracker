<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ThresholdSettings extends Settings
{
    public bool $absolute_enabled;

    public ?string $absolute_download;

    public ?string $absolute_upload;

    public ?string $absolute_ping;

    public static function group(): string
    {
        return 'threshold';
    }
}
