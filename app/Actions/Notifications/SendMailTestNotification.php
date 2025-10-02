<?php

namespace App\Actions\Notifications;

use App\Mail\Test as TestMail;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class SendMailTestNotification
{
    use AsAction;

    public function handle(array $recipients)
    {
        if (! count($recipients)) {
            Notification::make()
                ->title(__('translations.notifications.mail.add'))
                ->warning()
                ->send();

            return;
        }

        foreach ($recipients as $recipient) {
            Mail::to($recipient)
                ->send(new TestMail);
        }

        Notification::make()
            ->title(__('translations.notifications.mail.sent'))
            ->success()
            ->send();
    }
}
