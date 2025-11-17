<?php

namespace App\Actions\Notifications;

use App\Models\User;
use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class SendDatabaseTestNotification
{
    use AsAction;

    public function handle(User $user)
    {
        Notification::make()
            ->title(__('settings/notifications.test_notifications.database.received'))
            ->body(__('settings/notifications.test_notifications.database.pong'))
            ->success()
            ->sendToDatabase($user);

        Notification::make()
            ->title(__('settings/notifications.test_notifications.database.sent'))
            ->body(__('settings/notifications.test_notifications.database.ping'))
            ->success()
            ->send();
    }
}
