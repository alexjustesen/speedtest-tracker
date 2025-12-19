<?php

namespace App\Livewire;

use App\Settings\NotificationSettings;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DeprecatedNotificationChannelsBanner extends Component
{
    #[Computed]
    public function hasDeprecatedChannels(): bool
    {
        $settings = app(NotificationSettings::class);

        return $settings->discord_enabled
            || $settings->gotify_enabled
            || $settings->healthcheck_enabled
            || $settings->ntfy_enabled
            || $settings->pushover_enabled
            || $settings->slack_enabled
            || $settings->telegram_enabled;
    }

    #[Computed]
    public function deprecatedChannelsList(): array
    {
        $settings = app(NotificationSettings::class);
        $channels = [];

        if ($settings->discord_enabled) {
            $channels[] = 'Discord';
        }

        if ($settings->gotify_enabled) {
            $channels[] = 'Gotify';
        }

        if ($settings->healthcheck_enabled) {
            $channels[] = 'Healthchecks';
        }

        if ($settings->ntfy_enabled) {
            $channels[] = 'Ntfy';
        }

        if ($settings->pushover_enabled) {
            $channels[] = 'Pushover';
        }

        if ($settings->slack_enabled) {
            $channels[] = 'Slack';
        }

        if ($settings->telegram_enabled) {
            $channels[] = 'Telegram';
        }

        return $channels;
    }

    public function render()
    {
        return view('livewire.deprecated-notification-channels-banner');
    }
}
