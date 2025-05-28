<?php

namespace App\Listeners\Mail;

use App\Events\SpeedtestCompleted;
use App\Mail\SpeedtestCompletedMail;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSpeedtestCompletedNotification
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->mail_enabled) {
            return;
        }

        if (! $notificationSettings->mail_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->mail_recipients)) {
            Log::warning('Mail recipients not found, check mail notification channel settings.');

            return;
        }

        foreach ($notificationSettings->mail_recipients as $recipient) {
            Mail::to($recipient)
                ->send(new SpeedtestCompletedMail($event->result));
        }
    }
}
