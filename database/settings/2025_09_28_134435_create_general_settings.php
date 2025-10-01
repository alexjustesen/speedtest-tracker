<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.app_name', config('app.name'));
        $this->migrator->add('general.asset_url', config('app.asset_url'));
        $this->migrator->add('general.app_timezone', config('app.timezone'));
        $this->migrator->add('general.chart_begin_at_zero', config('app.chart_begin_at_zero'));
        $this->migrator->add('general.chart_datetime_format', config('app.chart_datetime_format'));
        $this->migrator->add('general.datetime_format', config('app.datetime_format'));
        $this->migrator->add('general.display_timezone', config('app.display_timezone'));
        $this->migrator->add('general.public_dashboard', config('speedtest.public_dashboard'));
        $this->migrator->add('general.speedtest_skip_ips', config('speedtest.skip_ips'));
        $this->migrator->add('general.speedtest_schedule', config('speedtest.schedule'));
        $this->migrator->add('general.speedtest_servers', config('speedtest.servers'));
        $this->migrator->add('general.speedtest_blocked_servers', config('speedtest.blocked_servers'));
        $this->migrator->add('general.speedtest_interface', config('speedtest.interface'));
        $this->migrator->add('general.speedtest_checkinternet_url', config('speedtest.checkinternet_url'));
        $this->migrator->add('general.threshold_enabled', config('speedtest.threshold_enabled'));
        $this->migrator->add('general.threshold_download', config('speedtest.threshold_download'));
        $this->migrator->add('general.threshold_upload', config('speedtest.threshold_upload'));
        $this->migrator->add('general.threshold_ping', config('speedtest.threshold_ping'));
        $this->migrator->add('general.prune_results_older_than', config('speedtest.prune_results_older_than'));
        $this->migrator->add('general.api_rate_limit', config('api.rate_limit'));
    }
};
