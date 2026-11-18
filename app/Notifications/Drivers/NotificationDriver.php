<?php

namespace App\Notifications\Drivers;

use App\Actions\Notifications\BuildNotificationMessage;
use App\Models\NotificationChannel;
use App\Notifications\NotificationMessage;

abstract class NotificationDriver
{
    /**
     * Get the form components for this driver's config, relative to the `config` state path.
     *
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    abstract public function configSchema(): array;

    /**
     * Deliver the message through the channel, throwing when delivery fails.
     */
    abstract public function send(NotificationChannel $channel, NotificationMessage $message): void;

    /**
     * Send a test message through the channel, throwing when delivery fails.
     */
    public function test(NotificationChannel $channel): void
    {
        $this->send($channel, BuildNotificationMessage::test());
    }

    /**
     * Determine if delivery is queued by the driver itself, so the outcome is
     * recorded later instead of when send() returns.
     */
    public function deliversAsynchronously(): bool
    {
        return false;
    }
}
