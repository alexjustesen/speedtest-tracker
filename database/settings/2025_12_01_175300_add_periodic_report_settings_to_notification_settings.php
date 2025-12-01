<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.mail_daily_average_enabled', false);
        $this->migrator->add('notification.mail_weekly_average_enabled', false);
        $this->migrator->add('notification.mail_monthly_average_enabled', false);
    }
};
