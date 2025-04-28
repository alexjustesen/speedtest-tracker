<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.database_on_speedtest_failed', false);
        $this->migrator->add('notification.apprise_on_speedtest_failed', false);
        $this->migrator->add('notification.webhook_on_speedtest_failed', false);
        $this->migrator->add('notification.mail_on_speedtest_failed', false);
    }
};
