<?php

use App\Enums\UserRole;
use App\Filament\Pages\Settings\Speedtests;
use App\Models\User;
use App\Settings\SpeedtestSettings;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));

    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

test('schedules, sources and measurement settings are saved', function () {
    Livewire::test(Speedtests::class)
        ->fillForm([
            'default_service' => 'yandex',
            'schedules' => [
                ['service' => 'yandex', 'cron' => '*/30 * * * *', 'enabled' => true],
                ['service' => 'iperf3', 'cron' => '0 * * * *', 'target' => ' iperf.example.com:5201 ', 'enabled' => false],
            ],
            // Simple repeaters keep each item under the name of their field.
            'iperf3_servers' => [['server' => 'iperf.example.com:5201']],
            'file_urls' => [['url' => 'https://example.com/100MB.bin']],
            'http_duration' => '15',
            'http_streams' => '6',
            'iperf3_duration' => '5',
            'iperf3_streams' => '2',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(SpeedtestSettings::class)->refresh();

    expect($settings->default_service)->toBe('yandex')
        ->and($settings->schedules)->toBe([
            ['enabled' => true, 'service' => 'yandex', 'cron' => '*/30 * * * *', 'target' => null],
            ['enabled' => false, 'service' => 'iperf3', 'cron' => '0 * * * *', 'target' => 'iperf.example.com:5201'],
        ])
        ->and($settings->iperf3_servers)->toBe(['iperf.example.com:5201'])
        ->and($settings->file_urls)->toBe(['https://example.com/100MB.bin'])
        ->and($settings->http_duration)->toBe(15)
        ->and($settings->http_streams)->toBe(6)
        ->and($settings->iperf3_duration)->toBe(5)
        ->and($settings->iperf3_streams)->toBe(2);
});

test('invalid schedules and sources are rejected', function () {
    Livewire::test(Speedtests::class)
        ->fillForm([
            'schedules' => [
                ['service' => 'file', 'cron' => 'every hour', 'target' => 'not a url', 'enabled' => true],
            ],
            'iperf3_servers' => [['server' => 'iperf.example.com:abc']],
            'http_duration' => '120',
        ])
        ->call('save')
        ->assertHasFormErrors([
            'schedules.0.cron',
            'schedules.0.target',
            'iperf3_servers.0.server',
            'http_duration',
        ]);
});

test('the page is only available to admins', function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::User]));

    $this->get(Speedtests::getUrl())->assertForbidden();
});

test('the environment schedule is mentioned only when it is set', function (mixed $schedule, bool $shown) {
    config(['speedtest.schedule' => $schedule]);

    $page = Livewire::test(Speedtests::class);

    $shown
        ? $page->assertSee('SPEEDTEST_SCHEDULE')
        : $page->assertDontSee('SPEEDTEST_SCHEDULE');
})->with([
    ['0 * * * *', true],
    [false, false],
    ['false', false],
    [null, false],
]);
