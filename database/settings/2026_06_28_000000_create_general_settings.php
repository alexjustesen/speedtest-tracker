<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.default_chart_range', config('speedtest.default_chart_range'));
        $this->migrator->add('general.prune_results_older_than', config('speedtest.prune_results_older_than'));
        $this->migrator->add('general.datetime_format', config('app.datetime_format'));
        $this->migrator->add('general.chart_datetime_format', config('app.chart_datetime_format'));
        $this->migrator->add('general.display_timezone', config('app.display_timezone'));
        $this->migrator->add('general.chart_begin_at_zero', config('app.chart_begin_at_zero'));
        $this->migrator->add('general.speedtest_external_ip_url', config('speedtest.preflight.external_ip_url'));
        $this->migrator->add('general.speedtest_internet_check_hostname', config('speedtest.preflight.internet_check_hostname'));
    }
};
