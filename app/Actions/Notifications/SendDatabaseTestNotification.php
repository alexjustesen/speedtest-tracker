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
        $user->notify(
            Notification::make()
                ->title(__('notifications.database.received'))
                ->body(__('notifications.database.pong'))
                ->success()
                ->toDatabase(),
        );

        Notification::make()
            ->title(__('notifications.database.sent'))
            ->body(__('notifications.database.ping'))
            ->success()
            ->send();
    }
}
