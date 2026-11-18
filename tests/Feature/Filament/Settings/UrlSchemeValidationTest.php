<?php

use App\Enums\UserRole;
use App\Filament\Pages\Settings\DataIntegration;
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
