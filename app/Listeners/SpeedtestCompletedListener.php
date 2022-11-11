<?php

namespace App\Listeners;

use App\Events\ResultCreated;
use App\Mail\SpeedtestCompletedMail;
use App\Settings\NotificationSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class SpeedtestCompletedListener
{
    public $notificationSettings;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->notificationSettings = new (NotificationSettings::class);
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ResultCreated  $event
     * @return void
     */
    public function handle(ResultCreated $event)
    {
        if ($this->notificationSettings->database_enabled) {
            if ($this->notificationSettings->database_on_speedtest_run) {
                Notification::make()
                    ->title('Speedtest completed')
                    ->success()
                    ->sendToDatabase($event->user);
            }
        }

        if ($this->notificationSettings->mail_enabled) {
            if ($this->notificationSettings->mail_on_speedtest_run && count($this->notificationSettings->mail_recipients)) {
                foreach ($this->notificationSettings->mail_recipients as $recipient) {
                    Mail::to($recipient)
                        ->send(new SpeedtestCompletedMail($event->result));
                }
            }
        }

    }
}
