<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateThresholdsSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('threshold.absolute_enabled', config('speedtest.threshold_enabled'));
        $this->migrator->add('threshold.absolute_download', config('speedtest.threshold_download'));
        $this->migrator->add('threshold.absolute_upload', config('speedtest.threshold_upload'));
        $this->migrator->add('threshold.absolute_ping', config('speedtest.threshold_ping'));
    }
}
