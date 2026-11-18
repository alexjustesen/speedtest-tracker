<?php

namespace App\Actions\Notifications;

use App\Models\NotificationChannel;
use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class SendTestNotification
{
    use AsAction;

    /**
     * Send a test message through the channel and report the outcome in the UI.
     */
    public function handle(NotificationChannel $channel): void
    {
        $driver = $channel->type->driver();

        try {
            $driver->test($channel);
        } catch (Throwable $e) {
            Notification::make()
                ->title(__('notification_channels.test_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        if ($driver->deliversAsynchronously()) {
            Notification::make()
                ->title(__('notification_channels.test_queued'))
                ->body(__('notification_channels.test_queued_body'))
                ->success()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('notification_channels.test_sent'))
            ->success()
            ->send();
    }
}
