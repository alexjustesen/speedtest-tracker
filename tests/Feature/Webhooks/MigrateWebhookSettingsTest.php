<?php

use App\Enums\WebhookEvent;
use App\Models\Webhook;
use Illuminate\Support\Facades\DB;

/**
 * Set a notification setting value directly in the settings table.
 */
function setWebhookSetting(string $name, mixed $value): void
{
    DB::table('settings')->updateOrInsert(
        ['group' => 'notification', 'name' => $name],
        ['payload' => json_encode($value), 'locked' => false, 'updated_at' => now()],
    );
}

function runWebhookSettingsMigration(): void
{
    $migration = require base_path('database/migrations/2026_05_29_000002_migrate_webhook_settings_to_table.php');
    $migration->up();
}

it('migrates configured webhook urls into the webhooks table with mapped events', function () {
    setWebhookSetting('webhook_enabled', true);
    setWebhookSetting('webhook_on_speedtest_run', true);
    setWebhookSetting('webhook_on_threshold_failure', true);
    setWebhookSetting('webhook_urls', [
        ['url' => 'https://example.com/hook-a'],
        ['url' => 'https://example.com/hook-b'],
    ]);

    runWebhookSettingsMigration();

    expect(Webhook::count())->toBe(2);

    $webhook = Webhook::where('url', 'https://example.com/hook-a')->first();

    expect($webhook->enabled)->toBeTrue()
        ->and($webhook->events)->toBe([
            WebhookEvent::Completed->value,
            WebhookEvent::BenchmarkUnhealthy->value,
        ]);
});

it('maps only the enabled triggers to events', function () {
    setWebhookSetting('webhook_enabled', false);
    setWebhookSetting('webhook_on_speedtest_run', true);
    setWebhookSetting('webhook_on_threshold_failure', false);
    setWebhookSetting('webhook_urls', [['url' => 'https://example.com/hook']]);

    runWebhookSettingsMigration();

    $webhook = Webhook::sole();

    expect($webhook->enabled)->toBeFalse()
        ->and($webhook->events)->toBe([WebhookEvent::Completed->value]);
});

it('creates no webhooks when none were configured', function () {
    setWebhookSetting('webhook_enabled', false);
    setWebhookSetting('webhook_on_speedtest_run', false);
    setWebhookSetting('webhook_on_threshold_failure', false);
    setWebhookSetting('webhook_urls', null);

    runWebhookSettingsMigration();

    expect(Webhook::count())->toBe(0);
});
