<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateMailNotificationSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.mail_enabled', false);
        $this->migrator->add('notification.mail_on_speedtest_run', false);
        $this->migrator->add('notification.mail_on_threshold_failure', false);
        $this->migrator->add('notification.mail_recipients', null);
    }
}
