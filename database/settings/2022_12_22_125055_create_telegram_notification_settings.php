<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateTelegramNotificationSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.telegram_enabled', false);
        $this->migrator->add('notification.telegram_on_speedtest_run', false);
        $this->migrator->add('notification.telegram_on_threshold_failure', false);
        $this->migrator->add('notification.telegram_recipients', null);
    }
}
