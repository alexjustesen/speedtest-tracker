<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.auth_enabled', true);
        $this->migrator->add('general.site_name', 'Speedtest Tracker');
        $this->migrator->add('general.speedtest_schedule', '');
        $this->migrator->add('general.speedtest_server', null);
        $this->migrator->add('general.timezone', 'UTC');
    }
}
