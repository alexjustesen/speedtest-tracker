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
                ->title(__('translations.notifications.database.received'))
                ->body(__('translations.notifications.database.pong'))
                ->success()
                ->toDatabase(),
        );

        Notification::make()
            ->title(__('translations.notifications.database.sent'))
            ->body(__('translations.notifications.database.ping'))
            ->success()
            ->send();
    }
}
