<?php

use App\Enums\NotificationChannelType;
use App\Enums\NotificationEvent;
use App\Models\NotificationChannel;
use Illuminate\Support\Facades\DB;

/**
 * Set a legacy notification setting value directly in the settings table.
 */
function setNotificationSetting(string $name, mixed $value): void
{
    DB::table('settings')->updateOrInsert(
        ['group' => 'notification', 'name' => $name],
        ['payload' => json_encode($value), 'locked' => false, 'updated_at' => now()],
    );
}

function runNotificationChannelsMigration(): void
{
    $migration = require base_path('database/migrations/2026_10_19_000002_migrate_notifications_to_channels.php');
    $migration->up();
}

beforeEach(function () {
    NotificationChannel::query()->delete();

    foreach (['database', 'mail', 'apprise', 'webhook'] as $channel) {
        setNotificationSetting("{$channel}_enabled", false);
        setNotificationSetting("{$channel}_on_speedtest_run", false);
        setNotificationSetting("{$channel}_on_benchmark_failure", false);
    }

    setNotificationSetting('webhook_on_threshold_failure', false);
    setNotificationSetting('webhook_urls', null);

    setNotificationSetting('mail_recipients', null);
    setNotificationSetting('apprise_server_url', null);
    setNotificationSetting('apprise_channel_urls', null);
    setNotificationSetting('apprise_verify_ssl', true);
});

it('creates no channels when nothing was configured', function () {
    runNotificationChannelsMigration();

    expect(NotificationChannel::count())->toBe(0)
        ->and(DB::table('settings')->where('group', 'notification')->count())->toBe(0);
});

it('migrates the mail and apprise settings with their triggers', function () {
    setNotificationSetting('database_enabled', true);
    setNotificationSetting('database_on_speedtest_run', true);

    setNotificationSetting('mail_enabled', true);
    setNotificationSetting('mail_on_benchmark_failure', true);
    setNotificationSetting('mail_recipients', [['email_address' => 'ops@example.com']]);

    setNotificationSetting('apprise_enabled', true);
    setNotificationSetting('apprise_on_speedtest_run', true);
    setNotificationSetting('apprise_on_benchmark_failure', true);
    setNotificationSetting('apprise_server_url', 'http://apprise:8000/notify');
    setNotificationSetting('apprise_verify_ssl', false);
    setNotificationSetting('apprise_channel_urls', [['channel_url' => 'discord://a/b']]);

    runNotificationChannelsMigration();

    $mail = NotificationChannel::where('type', NotificationChannelType::Mail)->sole();
    $apprise = NotificationChannel::where('type', NotificationChannelType::Apprise)->sole();

    expect(NotificationChannel::count())->toBe(2)
        ->and($mail->events)->toBe([NotificationEvent::BenchmarkUnhealthy->value])
        ->and($mail->include_unscheduled)->toBeFalse()
        ->and($mail->config('recipients'))->toBe(['ops@example.com'])
        ->and($apprise->events)->toBe([NotificationEvent::Completed->value, NotificationEvent::BenchmarkUnhealthy->value])
        ->and($apprise->config('server_url'))->toBe('http://apprise:8000/notify')
        ->and($apprise->config('channel_urls'))->toBe(['discord://a/b'])
        ->and($apprise->config('verify_ssl'))->toBeFalse()
        ->and(DB::table('settings')->where('group', 'notification')->count())->toBe(0);
});

it('keeps configured but disabled channels as disabled', function () {
    setNotificationSetting('mail_recipients', [['email_address' => 'ops@example.com']]);

    runNotificationChannelsMigration();

    expect(NotificationChannel::sole()->enabled)->toBeFalse();
});

it('migrates each legacy webhook url into a webhook channel', function () {
    setNotificationSetting('webhook_enabled', true);
    setNotificationSetting('webhook_on_speedtest_run', true);
    setNotificationSetting('webhook_on_threshold_failure', true);
    setNotificationSetting('webhook_urls', [
        ['url' => 'https://example.com/hook-a'],
        ['url' => 'https://hooks.example.org/hook-b'],
    ]);

    runNotificationChannelsMigration();

    $channels = NotificationChannel::where('type', NotificationChannelType::Webhook)->orderBy('id')->get();

    expect($channels)->toHaveCount(2)
        ->and($channels[0]->name)->toBe('example.com')
        ->and($channels[0]->config('url'))->toBe('https://example.com/hook-a')
        ->and($channels[0]->config('secret'))->toBeNull()
        ->and($channels[0]->enabled)->toBeTrue()
        ->and($channels[0]->events)->toBe([NotificationEvent::Completed->value, NotificationEvent::BenchmarkUnhealthy->value])
        ->and($channels[0]->include_unscheduled)->toBeFalse()
        ->and($channels[1]->config('url'))->toBe('https://hooks.example.org/hook-b');
});

it('keeps disabled legacy webhooks as disabled channels', function () {
    setNotificationSetting('webhook_on_speedtest_run', true);
    setNotificationSetting('webhook_urls', [['url' => 'https://example.com/hook']]);

    runNotificationChannelsMigration();

    $channel = NotificationChannel::sole();

    expect($channel->enabled)->toBeFalse()
        ->and($channel->events)->toBe([NotificationEvent::Completed->value]);
});
