<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateThresholdsSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('threshold.absolute_enabled', false);
        $this->migrator->add('threshold.absolute_download', null);
        $this->migrator->add('threshold.absolute_upload', null);
        $this->migrator->add('threshold.absolute_ping', null);
    }
}
