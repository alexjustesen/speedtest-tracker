<?php

use App\Enums\NotificationChannelType;
use App\Enums\NotificationEvent;
use App\Enums\UserRole;
use App\Filament\Resources\NotificationChannels\NotificationChannelResource;
use App\Filament\Resources\NotificationChannels\Pages\ListNotificationChannels;
use App\Models\NotificationChannel;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\WebhookServer\CallWebhookJob;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create(['role' => UserRole::Admin]));
});

it('lists notification channels for an admin', function () {
    $channels = NotificationChannel::factory()->count(3)->create();

    Livewire::test(ListNotificationChannels::class)
        ->assertOk()
        ->assertCanSeeTableRecords($channels);
});

it('creates a webhook channel through the form', function () {
    Livewire::test(ListNotificationChannels::class)
        ->callAction('create', [
            'name' => 'Home Assistant',
            'type' => NotificationChannelType::Webhook->value,
            'config' => ['url' => 'http://homeassistant:8123/api/webhook/speedtest', 'secret' => null],
            'events' => [NotificationEvent::Completed->value, NotificationEvent::Failed->value],
            'enabled' => true,
        ])
        ->assertHasNoActionErrors();

    $channel = NotificationChannel::sole();

    expect($channel->type)->toBe(NotificationChannelType::Webhook)
        ->and($channel->config('url'))->toBe('http://homeassistant:8123/api/webhook/speedtest')
        ->and($channel->include_unscheduled)->toBeTrue();
});

it('creates an apprise channel with tags and basic auth', function () {
    Livewire::test(ListNotificationChannels::class)
        ->callAction('create', [
            'name' => 'Discord alerts',
            'type' => NotificationChannelType::Apprise->value,
            'config' => [
                'server_url' => 'https://apprise.example.com/notify/speedtest',
                'username' => 'user',
                'password' => 'secret',
                'verify_ssl' => true,
                'channel_urls' => [],
                'tags' => ['alerts'],
            ],
            'events' => [NotificationEvent::BenchmarkUnhealthy->value, NotificationEvent::BenchmarkRecovered->value],
            'enabled' => true,
        ])
        ->assertHasNoActionErrors();

    $channel = NotificationChannel::sole();

    expect($channel->config('tags'))->toBe(['alerts'])
        ->and($channel->config('username'))->toBe('user')
        ->and($channel->include_unscheduled)->toBeFalse();
});

it('requires apprise service urls or tags', function () {
    Livewire::test(ListNotificationChannels::class)
        ->callAction('create', [
            'name' => 'Apprise',
            'type' => NotificationChannelType::Apprise->value,
            'config' => [
                'server_url' => 'http://apprise:8000/notify',
                'channel_urls' => [],
                'tags' => [],
            ],
            'events' => [NotificationEvent::Completed->value],
        ])
        ->assertHasActionErrors(['config.channel_urls', 'config.tags']);
});

it('rejects invalid apprise server and service urls', function () {
    Livewire::test(ListNotificationChannels::class)
        ->callAction('create', [
            'name' => 'Apprise',
            'type' => NotificationChannelType::Apprise->value,
            'config' => [
                'server_url' => 'ftp://apprise:8000/notify',
                'channel_urls' => ['https://discord.com/api/webhooks/a/b'],
            ],
            'events' => [NotificationEvent::Completed->value],
        ])
        ->assertHasActionErrors(['config.server_url' => 'url']);
});

it('rejects non-HTTP webhook URL protocols', function (string $url) {
    Livewire::test(ListNotificationChannels::class)
        ->callAction('create', [
            'name' => 'Webhook',
            'type' => NotificationChannelType::Webhook->value,
            'config' => ['url' => $url],
            'events' => [NotificationEvent::Completed->value],
        ])
        ->assertHasActionErrors(['config.url' => 'url']);
})->with([
    'FTP' => 'ftp://webhook-receiver/hook',
    'file' => 'file:///tmp/webhook',
]);

it('offers every event to every channel type', function () {
    Livewire::test(ListNotificationChannels::class)
        ->callAction('create', [
            'name' => 'Mail',
            'type' => NotificationChannelType::Mail->value,
            'config' => ['recipients' => [['email' => 'ops@example.com']]],
            'events' => [NotificationEvent::Failed->value, NotificationEvent::BenchmarkRecovered->value],
        ])
        ->assertHasNoActionErrors();

    $channel = NotificationChannel::sole();

    expect($channel->events)->toBe([
        NotificationEvent::Failed->value,
        NotificationEvent::BenchmarkRecovered->value,
    ])->and($channel->config('recipients'))->toBe(['ops@example.com']);
});

it('rejects unknown events', function () {
    Livewire::test(ListNotificationChannels::class)
        ->callAction('create', [
            'name' => 'Mail',
            'type' => NotificationChannelType::Mail->value,
            'config' => ['recipients' => [['email' => 'ops@example.com']]],
            'events' => ['speedtest_exploded'],
        ])
        ->assertHasActionErrors(['events.0']);
});

it('loads and keeps the existing config when editing a channel', function () {
    $mail = NotificationChannel::factory()->mail(['a@example.com', 'b@example.com'])->create();
    $apprise = NotificationChannel::factory()->apprise(['password' => 'secret'])->create();

    Livewire::test(ListNotificationChannels::class)
        ->mountAction(TestAction::make('edit')->table($mail))
        ->assertSchemaStateSet(fn (array $state): bool => array_values(array_column($state['config']['recipients'], 'email')) === ['a@example.com', 'b@example.com'], 'mountedActionSchema0')
        ->callMountedAction()
        ->assertHasNoActionErrors()
        ->mountAction(TestAction::make('edit')->table($apprise))
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect($mail->refresh()->config('recipients'))->toBe(['a@example.com', 'b@example.com'])
        ->and($apprise->refresh()->config('server_url'))->toBe('http://apprise:8000/notify')
        ->and($apprise->config('channel_urls'))->toBe(['discord://webhook-id/webhook-token'])
        ->and($apprise->config('password'))->toBe('secret');
});

it('sends a synchronous test notification and reports the outcome', function () {
    Http::fake(['*' => Http::response(['success' => true])]);

    $channel = NotificationChannel::factory()->apprise()->create();

    Livewire::test(ListNotificationChannels::class)
        ->callAction(TestAction::make('test')->table($channel))
        ->assertNotified(__('notification_channels.test_sent'));

    Http::assertSentCount(1);
});

it('reports a failed test notification', function () {
    Http::fake(['*' => Http::response(['error' => 'Unauthorized'], 401)]);

    $channel = NotificationChannel::factory()->apprise()->create();

    Livewire::test(ListNotificationChannels::class)
        ->callAction(TestAction::make('test')->table($channel))
        ->assertNotified(__('notification_channels.test_failed'));
});

it('queues a test webhook', function () {
    Bus::fake([CallWebhookJob::class]);

    $channel = NotificationChannel::factory()->webhook()->create();

    Livewire::test(ListNotificationChannels::class)
        ->callAction(TestAction::make('test')->table($channel))
        ->assertNotified(__('notification_channels.test_queued'));

    Bus::assertDispatchedTimes(CallWebhookJob::class, 1);
});

it('denies access to non-admin users', function () {
    actingAs(User::factory()->create(['role' => UserRole::User]));

    expect(NotificationChannelResource::canAccess())->toBeFalse();
});
