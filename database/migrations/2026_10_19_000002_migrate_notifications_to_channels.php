<?php

use App\Enums\NotificationChannelType;
use App\Enums\NotificationEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = DB::table('settings')
            ->where('group', 'notification')
            ->pluck('payload', 'name')
            ->map(fn ($payload) => json_decode($payload, true));

        $this->migrateMailChannel($settings);
        $this->migrateAppriseChannel($settings);
        $this->migrateWebhookChannels($settings);

        DB::table('settings')->where('group', 'notification')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('notification_channels')->delete();
    }

    /**
     * Migrate the mail notification settings, if enabled or configured.
     */
    private function migrateMailChannel(Collection $settings): void
    {
        $recipients = collect($settings['mail_recipients'] ?? [])
            ->pluck('email_address')
            ->filter()
            ->values()
            ->all();

        $enabled = (bool) ($settings['mail_enabled'] ?? false);

        if (! $enabled && empty($recipients)) {
            return;
        }

        $this->insertChannel(
            name: 'Mail',
            type: NotificationChannelType::Mail,
            enabled: $enabled,
            config: ['recipients' => $recipients],
            events: $this->mapTriggers($settings, 'mail'),
        );
    }

    /**
     * Migrate the Apprise notification settings, if enabled or configured.
     */
    private function migrateAppriseChannel(Collection $settings): void
    {
        $serverUrl = $settings['apprise_server_url'] ?? null;

        $enabled = (bool) ($settings['apprise_enabled'] ?? false);

        if (! $enabled && blank($serverUrl)) {
            return;
        }

        $this->insertChannel(
            name: 'Apprise',
            type: NotificationChannelType::Apprise,
            enabled: $enabled,
            config: [
                'server_url' => $serverUrl,
                'channel_urls' => collect($settings['apprise_channel_urls'] ?? [])
                    ->pluck('channel_url')
                    ->filter()
                    ->values()
                    ->all(),
                'tags' => [],
                'username' => null,
                'password' => null,
                'verify_ssl' => (bool) ($settings['apprise_verify_ssl'] ?? true),
            ],
            events: $this->mapTriggers($settings, 'apprise'),
        );
    }

    /**
     * Migrate each legacy webhook URL into its own webhook channel. Legacy
     * webhooks only fired for scheduled speedtests, so that's kept.
     */
    private function migrateWebhookChannels(Collection $settings): void
    {
        $enabled = (bool) ($settings['webhook_enabled'] ?? false);
        $events = $this->mapTriggers($settings, 'webhook');

        collect($settings['webhook_urls'] ?? [])
            ->pluck('url')
            ->filter()
            ->unique()
            ->each(fn (string $url) => $this->insertChannel(
                name: parse_url($url, PHP_URL_HOST) ?: 'Webhook',
                type: NotificationChannelType::Webhook,
                enabled: $enabled,
                config: ['url' => $url, 'secret' => null],
                events: $events,
            ));
    }

    /**
     * Map the legacy per-channel trigger toggles to notification events.
     *
     * @return array<int, string>
     */
    private function mapTriggers(Collection $settings, string $prefix): array
    {
        return array_values(array_filter([
            ($settings["{$prefix}_on_speedtest_run"] ?? false) ? NotificationEvent::Completed->value : null,
            // Webhooks were never renamed and still use the "threshold" key.
            (($settings["{$prefix}_on_benchmark_failure"] ?? false) || ($settings["{$prefix}_on_threshold_failure"] ?? false))
                ? NotificationEvent::BenchmarkUnhealthy->value
                : null,
        ]));
    }

    /**
     * Insert a notification channel row, encrypting its config like the model cast does.
     *
     * @param  array<string, mixed>  $config
     * @param  array<int, string>  $events
     */
    private function insertChannel(
        string $name,
        NotificationChannelType $type,
        bool $enabled,
        array $config,
        array $events,
    ): void {
        DB::table('notification_channels')->insert([
            'name' => $name,
            'type' => $type->value,
            'enabled' => $enabled,
            'config' => Crypt::encryptString(json_encode($config)),
            'events' => json_encode(Arr::wrap($events)),
            'include_unscheduled' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
