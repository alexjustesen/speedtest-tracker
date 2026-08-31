<?php

use App\Enums\UserRole;
use App\Filament\Pages\Settings\DataIntegration;
use App\Filament\Pages\Settings\Notification;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create(['role' => UserRole::Admin]));
});

it('accepts HTTP and HTTPS InfluxDB URLs', function (string $url) {
    Livewire::test(DataIntegration::class)
        ->fillForm([
            'influxdb_v2_enabled' => true,
            'influxdb_v2_url' => $url,
            'influxdb_v2_org' => 'speedtest-tracker',
            'influxdb_v2_bucket' => 'results',
            'influxdb_v2_token' => 'token',
            'influxdb_v2_verify_ssl' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();
})->with([
    'HTTP container hostname' => 'http://influxdb:8086',
    'HTTPS loopback address' => 'https://127.0.0.1:8086',
]);

it('rejects non-HTTP InfluxDB URL protocols', function (string $url) {
    Livewire::test(DataIntegration::class)
        ->fillForm([
            'influxdb_v2_enabled' => true,
            'influxdb_v2_url' => $url,
            'influxdb_v2_org' => 'speedtest-tracker',
            'influxdb_v2_bucket' => 'results',
            'influxdb_v2_token' => 'token',
            'influxdb_v2_verify_ssl' => true,
        ])
        ->call('save')
        ->assertHasFormErrors(['influxdb_v2_url' => 'url']);
})->with([
    'FTP' => 'ftp://influxdb:8086',
    'file' => 'file:///tmp/influxdb.sock',
    'Gopher' => 'gopher://influxdb:70',
]);

it('accepts HTTP and HTTPS Apprise server URLs', function (string $url) {
    Livewire::test(Notification::class)
        ->fillForm([
            'apprise_enabled' => true,
            'apprise_server_url' => $url,
            'apprise_verify_ssl' => true,
            'apprise_on_speedtest_run' => true,
            'apprise_on_benchmark_failure' => false,
            'apprise_channel_urls' => [
                ['channel_url' => 'discord://webhook-id/webhook-token'],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();
})->with([
    'HTTP container hostname' => 'http://apprise:8000/notify',
    'HTTPS private address' => 'https://192.168.1.10:8000/notify',
]);

it('rejects non-HTTP Apprise server URL protocols', function (string $url) {
    Livewire::test(Notification::class)
        ->fillForm([
            'apprise_enabled' => true,
            'apprise_server_url' => $url,
            'apprise_verify_ssl' => true,
            'apprise_on_speedtest_run' => true,
            'apprise_on_benchmark_failure' => false,
            'apprise_channel_urls' => [
                ['channel_url' => 'discord://webhook-id/webhook-token'],
            ],
        ])
        ->call('save')
        ->assertHasFormErrors(['apprise_server_url' => 'url']);
})->with([
    'FTP' => 'ftp://apprise:8000/notify',
    'file' => 'file:///tmp/notify',
    'Gopher' => 'gopher://apprise:70/notify',
]);
