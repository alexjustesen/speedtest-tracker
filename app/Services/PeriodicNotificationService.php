<?php

namespace App\Services;

use App\Mail\PeriodicAverageMail;
use App\Notifications\Apprise\PeriodicAverageNotification;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class PeriodicNotificationService
{
    public function sendMail(
        NotificationSettings $settings,
        array $stats,
        string $period,
        string $periodLabel
    ): void {
        if (empty($settings->mail_recipients)) {
            return;
        }

        foreach ($settings->mail_recipients as $recipient) {
            Mail::to($recipient)->queue(
                new PeriodicAverageMail($stats, $period, $periodLabel)
            );
        }
    }

    public function sendApprise(
        NotificationSettings $settings,
        array $stats,
        string $period,
        string $periodLabel
    ): void {
        if (empty($settings->apprise_channel_urls)) {
            return;
        }

        $urls = collect($settings->apprise_channel_urls)->pluck('channel_url')->toArray();

        Notification::route('apprise_urls', $urls)
            ->notify(new PeriodicAverageNotification(
                $stats,
                $period,
                $periodLabel
            ));
    }
}
