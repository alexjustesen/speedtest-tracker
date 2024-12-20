<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.discord_user_mention', null);
    }

    public function down(): void
    {
        $this->migrator->delete('notification.discord_user_mention');
    }
};
