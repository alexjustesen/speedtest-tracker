<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DataMigrationSettings extends Settings
{
    public bool $bad_json_migrated;

    public static function group(): string
    {
        return 'data_migration';
    }
}
