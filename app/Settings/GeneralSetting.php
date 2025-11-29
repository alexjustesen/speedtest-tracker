<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSetting extends Settings
{
    // TODO: Add your settings properties here

    public static function group(): string
    {
        return 'general';
    }
}
