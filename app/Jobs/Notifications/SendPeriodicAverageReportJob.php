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
        public ReportPeriod $period,
        public bool $sendMail,
        public bool $sendApprise
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

        if ($this->sendMail) {
            $notificationService->sendMail($settings, $stats, $periodName, $periodLabel);
        }

        if ($this->sendApprise) {
            $notificationService->sendApprise($settings, $stats, $periodName, $periodLabel);
        }
    }
}
