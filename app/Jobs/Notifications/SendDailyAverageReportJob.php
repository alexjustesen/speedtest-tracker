<?php

namespace App\Jobs\Notifications;

use App\Mail\PeriodicAverageMail;
use App\Services\PeriodicReportService;
use App\Settings\NotificationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendDailyAverageReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(NotificationSettings $settings, PeriodicReportService $reportService): void
    {
        if (! $settings->mail_enabled || ! $settings->mail_daily_average_enabled) {
            return;
        }

        if (empty($settings->mail_recipients)) {
            return;
        }

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

        foreach ($settings->mail_recipients as $recipient) {
            Mail::to($recipient)->queue(
                new PeriodicAverageMail($stats, $period, $periodLabel, $serverStats)
            );
        }
    }
}
