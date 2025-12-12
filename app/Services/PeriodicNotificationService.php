<?php

namespace App\Services;

use App\Mail\PeriodicAverageMail;
use App\Notifications\Apprise\PeriodicAverageNotification;
use App\Settings\NotificationSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Spatie\WebhookServer\WebhookCall;

class PeriodicNotificationService
{
    public function sendMail(
        NotificationSettings $settings,
        array $stats,
        string $period,
        string $periodLabel,
        array $serverStats
    ): void {
        if (empty($settings->mail_recipients)) {
            return;
        }

        foreach ($settings->mail_recipients as $recipient) {
            Mail::to($recipient)->queue(
                new PeriodicAverageMail($stats, $period, $periodLabel, $serverStats)
            );
        }
    }

    public function sendApprise(
        NotificationSettings $settings,
        array $stats,
        string $period,
        string $periodLabel,
        array $serverStats
    ): void {
        if (empty($settings->apprise_channel_urls)) {
            return;
        }

        $urls = collect($settings->apprise_channel_urls)->pluck('channel_url')->toArray();

        Notification::route('apprise_urls', $urls)
            ->notify(new PeriodicAverageNotification(
                $stats,
                $period,
                $periodLabel,
                $serverStats
            ));
    }

    public function sendWebhook(
        NotificationSettings $settings,
        Carbon $start,
        Carbon $end,
        array $stats,
        string $period,
        string $periodLabel,
        array $serverStats
    ): void {
        if (empty($settings->webhook_urls)) {
            return;
        }

        foreach ($settings->webhook_urls as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload([
                    'site_name' => config('app.name'),
                    'period' => $period,
                    'period_label' => $periodLabel,
                    'start_date' => $start->toDateTimeString(),
                    'end_date' => $end->toDateTimeString(),
                    'stats' => $stats,
                    'server_stats' => $serverStats,
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatch();
        }
    }
}
