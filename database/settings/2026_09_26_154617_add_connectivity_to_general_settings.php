<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.external_ip_url', env('SPEEDTEST_CHECKINTERNET_URL') ?? env('SPEEDTEST_EXTERNAL_IP_URL', 'https://icanhazip.com'));
        $this->migrator->add('general.internet_check_hostname', env('SPEEDTEST_CHECKINTERNET_URL') ?? env('SPEEDTEST_INTERNET_CHECK_HOSTNAME', 'icanhazip.com'));
    }
};
