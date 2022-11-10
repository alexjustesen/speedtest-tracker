<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateThresholdsSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('threshold.absolute_enabled', false);
        $this->migrator->add('threshold.absolute_download', 0);
        $this->migrator->add('threshold.absolute_upload', 0);
        $this->migrator->add('threshold.absolute_ping', 0);
    }
}
