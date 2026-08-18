<?php

use App\Enums\UserRole;
use App\Filament\Pages\Settings\DataIntegration;
use App\Filament\Pages\Settings\Notification;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));

    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('accepts HTTP and HTTPS InfluxDB URLs, including container and loopback hosts', function (string $url) {
    Livewire::test(DataIntegration::class)
        ->fillForm([
            'influxdb_v2_enabled' => true,
            'influxdb_v2_url' => $url,
            'influxdb_v2_org' => 'speedtest',
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

it('rejects non-HTTP InfluxDB URL schemes', function (string $url) {
    Livewire::test(DataIntegration::class)
        ->fillForm([
            'influxdb_v2_enabled' => true,
            'influxdb_v2_url' => $url,
            'influxdb_v2_org' => 'speedtest',
            'influxdb_v2_bucket' => 'results',
            'influxdb_v2_token' => 'token',
        ])
        ->call('save')
        ->assertHasFormErrors(['influxdb_v2_url' => 'url']);
})->with([
    'FTP' => 'ftp://influxdb/results',
    'file' => 'file:///var/lib/influxdb/results',
    'Gopher' => 'gopher://influxdb/results',
]);

it('accepts HTTP and HTTPS URLs in every directly contacted notification form', function () {
    Livewire::test(Notification::class)
        ->fillForm([
            'webhook_enabled' => true,
            'webhook_urls' => [['url' => 'http://webhook:9000/speedtest']],
            'apprise_enabled' => true,
            'apprise_server_url' => 'https://127.0.0.1:8000/notify',
            'apprise_channel_urls' => [['channel_url' => 'discord://webhook-id/token']],
            'pushover_enabled' => true,
            'pushover_webhooks' => [[
                'url' => 'https://api.pushover.net/1/messages.json',
                'user_key' => 'user-key',
                'api_token' => 'api-token',
            ]],
            'discord_enabled' => true,
            'discord_webhooks' => [['url' => 'http://discord/webhook']],
            'gotify_enabled' => true,
            'gotify_webhooks' => [['url' => 'https://gotify/message?token=token']],
            'slack_enabled' => true,
            'slack_webhooks' => [['url' => 'http://slack/services/hook']],
            'ntfy_enabled' => true,
            'ntfy_webhooks' => [[
                'url' => 'https://ntfy',
                'topic' => 'speedtest',
            ]],
            'healthcheck_enabled' => true,
            'healthcheck_webhooks' => [['url' => 'http://healthcheck/ping']],
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('rejects non-HTTP URL schemes in directly contacted notification forms', function (
    array $formState,
    string $field,
) {
    Livewire::test(Notification::class)
        ->fillForm($formState)
        ->call('save')
        ->assertHasFormErrors([$field => 'url']);
})->with([
    'generic webhook using FTP' => [
        ['webhook_enabled' => true, 'webhook_urls' => [['url' => 'ftp://webhook/speedtest']]],
        'webhook_urls.0.url',
    ],
    'Apprise server using file' => [
        [
            'apprise_enabled' => true,
            'apprise_server_url' => 'file:///tmp/notify',
            'apprise_channel_urls' => [['channel_url' => 'discord://webhook-id/token']],
        ],
        'apprise_server_url',
    ],
    'Pushover using Gopher' => [
        [
            'pushover_enabled' => true,
            'pushover_webhooks' => [[
                'url' => 'gopher://pushover/messages',
                'user_key' => 'user-key',
                'api_token' => 'api-token',
            ]],
        ],
        'pushover_webhooks.0.url',
    ],
    'Discord using FTP' => [
        ['discord_enabled' => true, 'discord_webhooks' => [['url' => 'ftp://discord/webhook']]],
        'discord_webhooks.0.url',
    ],
    'Gotify using file' => [
        ['gotify_enabled' => true, 'gotify_webhooks' => [['url' => 'file:///tmp/gotify']]],
        'gotify_webhooks.0.url',
    ],
    'Slack using Gopher' => [
        ['slack_enabled' => true, 'slack_webhooks' => [['url' => 'gopher://slack/hook']]],
        'slack_webhooks.0.url',
    ],
    'ntfy using FTP' => [
        [
            'ntfy_enabled' => true,
            'ntfy_webhooks' => [['url' => 'ftp://ntfy', 'topic' => 'speedtest']],
        ],
        'ntfy_webhooks.0.url',
    ],
    'Healthcheck using file' => [
        ['healthcheck_enabled' => true, 'healthcheck_webhooks' => [['url' => 'file:///tmp/ping']]],
        'healthcheck_webhooks.0.url',
    ],
]);
