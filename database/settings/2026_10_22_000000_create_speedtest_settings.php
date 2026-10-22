<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSpeedtestSettings extends SettingsMigration
{
    /**
     * The environment variables only provide the initial values, the settings are managed in the UI afterwards.
     */
    public function up(): void
    {
        $list = fn (?string $value): array => array_values(array_filter(array_map('trim', explode(',', (string) $value))));

        $this->migrator->add('speedtest.default_service', config('speedtest.service') ?: 'ookla');
        $this->migrator->add('speedtest.schedules', []);
        $this->migrator->add('speedtest.iperf3_servers', $list(config('speedtest.iperf3.servers')));
        $this->migrator->add('speedtest.file_urls', $list(config('speedtest.file.urls')));
        $this->migrator->add('speedtest.http_duration', (int) config('speedtest.http.duration', 10));
        $this->migrator->add('speedtest.http_streams', (int) config('speedtest.http.streams', 4));
        $this->migrator->add('speedtest.iperf3_duration', (int) config('speedtest.iperf3.duration', 10));
        $this->migrator->add('speedtest.iperf3_streams', (int) config('speedtest.iperf3.streams', 4));
    }
}
