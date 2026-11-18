<?php

namespace App\Enums;

use App\Notifications\Drivers\AppriseDriver;
use App\Notifications\Drivers\MailDriver;
use App\Notifications\Drivers\NotificationDriver;
use App\Notifications\Drivers\WebhookDriver;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum NotificationChannelType: string implements HasIcon, HasLabel
{
    case Mail = 'mail';
    case Apprise = 'apprise';
    case Webhook = 'webhook';

    public function getLabel(): ?string
    {
        return __('enums.notification_channel_type.'.$this->value);
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Mail => 'tabler-mail',
            self::Apprise => 'tabler-send',
            self::Webhook => 'tabler-webhook',
        };
    }

    /**
     * Get the driver that delivers notifications for this channel type.
     */
    public function driver(): NotificationDriver
    {
        return app(match ($this) {
            self::Mail => MailDriver::class,
            self::Apprise => AppriseDriver::class,
            self::Webhook => WebhookDriver::class,
        });
    }

    /**
     * Determine if channels of this type include unscheduled (manual) speedtests by default.
     */
    public function includesUnscheduledByDefault(): bool
    {
        return $this === self::Webhook;
    }
}
