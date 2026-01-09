<?php

namespace App\Jobs\Notifications;

use App\Enums\ReportPeriod;
use App\Services\PeriodicNotificationService;
use App\Services\PeriodicReportService;
use App\Settings\NotificationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPeriodicAverageReportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ReportPeriod $period
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        NotificationSettings $settings,
        PeriodicReportService $reportService,
        PeriodicNotificationService $notificationService
    ): void {
        $start = $this->period->getStartDate();
        $end = $this->period->getEndDate();

        $results = $reportService->getResults($start, $end);

        if ($results->isEmpty()) {
            return;
        }

        $stats = $reportService->calculateStats($results);

        $periodName = $this->period->getName();
        $periodLabel = $this->period->getLabel();

        $mailEnabled = match ($this->period) {
            ReportPeriod::Daily => $settings->mail_enabled && $settings->mail_daily_average_enabled,
            ReportPeriod::Weekly => $settings->mail_enabled && $settings->mail_weekly_average_enabled,
            ReportPeriod::Monthly => $settings->mail_enabled && $settings->mail_monthly_average_enabled,
        };

        $appriseEnabled = match ($this->period) {
            ReportPeriod::Daily => $settings->apprise_enabled && $settings->apprise_daily_average_enabled,
            ReportPeriod::Weekly => $settings->apprise_enabled && $settings->apprise_weekly_average_enabled,
            ReportPeriod::Monthly => $settings->apprise_enabled && $settings->apprise_monthly_average_enabled,
        };

        if ($mailEnabled) {
            $notificationService->sendMail($settings, $stats, $periodName, $periodLabel);
        }

        if ($appriseEnabled) {
            $notificationService->sendApprise($settings, $stats, $periodName, $periodLabel);
        }
    }
}
