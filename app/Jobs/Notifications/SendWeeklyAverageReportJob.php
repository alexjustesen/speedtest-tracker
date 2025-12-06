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
use Spatie\WebhookServer\WebhookCall;

class SendWeeklyAverageReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(NotificationSettings $settings, PeriodicReportService $reportService): void
    {
        $start = now()->subWeek()->startOfWeek();
        $end = now()->subWeek()->endOfWeek();

        $results = $reportService->getResults($start, $end);

        if ($results->isEmpty()) {
            return;
        }

        $stats = $reportService->calculateStats($results);
        $serverStats = $reportService->calculateServerStats($results);

        $period = 'Weekly';
        $periodLabel = $start->format('M j').' - '.$end->format('M j, Y');

        // Send mail notifications
        if ($settings->mail_enabled && $settings->mail_weekly_average_enabled && ! empty($settings->mail_recipients)) {
            foreach ($settings->mail_recipients as $recipient) {
                Mail::to($recipient)->queue(
                    new PeriodicAverageMail($stats, $period, $periodLabel, $serverStats)
                );
            }
        }

        // Send Apprise notifications
        if ($settings->apprise_enabled && $settings->apprise_weekly_average_enabled && ! empty($settings->apprise_channel_urls)) {
            $urls = collect($settings->apprise_channel_urls)->pluck('channel_url')->toArray();

            Notification::route('apprise_urls', $urls)
                ->notify(new PeriodicAverageNotification($stats, $period, $periodLabel, is_array($serverStats) ? $serverStats : $serverStats->toArray()));
        }

        // Send webhook notifications
        if ($settings->webhook_enabled && $settings->webhook_weekly_average_enabled && ! empty($settings->webhook_urls)) {
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
                        'server_stats' => is_array($serverStats) ? $serverStats : $serverStats->toArray(),
                        'url' => url('/admin/results'),
                    ])
                    ->doNotSign()
                    ->dispatch();
            }
        }
    }
}
