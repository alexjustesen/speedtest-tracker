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
                ->title('Test database notification received!')
                ->body('You say pong')
                ->success()
                ->toDatabase(),
        );

        Notification::make()
            ->title('Test database notification sent.')
            ->body('I say ping')
            ->success()
            ->send();
    }
}
