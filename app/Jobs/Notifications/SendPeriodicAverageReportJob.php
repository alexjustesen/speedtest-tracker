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

        // Send mail notifications
        if ($this->period->isEnabledForMail($settings)) {
            $notificationService->sendMail($settings, $stats, $periodName, $periodLabel);
        }

        // Send Apprise notifications
        if ($this->period->isEnabledForApprise($settings)) {
            $notificationService->sendApprise($settings, $stats, $periodName, $periodLabel);
        }
    }
}
