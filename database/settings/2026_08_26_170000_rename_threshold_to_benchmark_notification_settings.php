<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class RenameThresholdToBenchmarkNotificationSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->rename('notification.database_on_threshold_failure', 'notification.database_on_benchmark_failure');
        $this->migrator->rename('notification.mail_on_threshold_failure', 'notification.mail_on_benchmark_failure');
        $this->migrator->rename('notification.apprise_on_threshold_failure', 'notification.apprise_on_benchmark_failure');
    }
}
