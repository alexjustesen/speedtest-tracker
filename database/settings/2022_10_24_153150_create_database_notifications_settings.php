<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateDatabaseNotificationsSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.database_enabled', true);
        $this->migrator->add('notification.database_on_speedtest_run', true);
        $this->migrator->add('notification.database_on_threshold_failure', true);
    }
}
