<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class DropThresholdsSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->delete('threshold.absolute_enabled');
        $this->migrator->delete('threshold.absolute_download');
        $this->migrator->delete('threshold.absolute_upload');
        $this->migrator->delete('threshold.absolute_ping');
    }
}
