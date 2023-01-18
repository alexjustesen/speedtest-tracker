<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddTimeFormatToGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.time_format', 'M j, Y G:i:s');
    }
}
