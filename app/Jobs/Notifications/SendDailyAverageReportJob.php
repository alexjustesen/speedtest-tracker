<?php

namespace App\Jobs\Notifications;

use App\Mail\PeriodicAverageMail;
use App\Notifications\Apprise\PeriodicAverageNotification;
use App\Services\PeriodicReportService;
use App\Settings\NotificationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendDailyAverageReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(NotificationSettings $settings, PeriodicReportService $reportService): void
    {
        $start = now()->subDay()->startOfDay();
        $end = now()->subDay()->endOfDay();

        $results = $reportService->getResults($start, $end);

        if ($results->isEmpty()) {
            return;
        }

        $stats = $reportService->calculateStats($results);
        $serverStats = $reportService->calculateServerStats($results);

        $period = 'Daily';
        $periodLabel = now()->subDay()->format('F j, Y');

        // Send mail notifications
        if ($settings->mail_enabled && $settings->mail_daily_average_enabled && ! empty($settings->mail_recipients)) {
            foreach ($settings->mail_recipients as $recipient) {
                Mail::to($recipient)->queue(
                    new PeriodicAverageMail($stats, $period, $periodLabel, $serverStats)
                );
            }
        }

        // Send Apprise notifications
        if ($settings->apprise_enabled && $settings->apprise_daily_average_enabled && ! empty($settings->apprise_channel_urls)) {
            $urls = collect($settings->apprise_channel_urls)->pluck('channel_url')->toArray();

            Notification::route('apprise_urls', $urls)
                ->notify(new PeriodicAverageNotification($stats, $period, $periodLabel, is_array($serverStats) ? $serverStats : $serverStats->toArray()));
        }
    }
}
