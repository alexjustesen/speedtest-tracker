<?php

namespace App\Jobs\Notifications;

use App\Enums\ReportPeriod;
use App\Services\PeriodicReportService;
use App\Settings\NotificationSettings;
use App\Traits\SendsPeriodicNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPeriodicAverageReportJob implements ShouldQueue
{
    use Queueable, SendsPeriodicNotifications;

    public function __construct(
        public ReportPeriod $period
    ) {}

    /**
     * Execute the job.
     */
    public function handle(NotificationSettings $settings, PeriodicReportService $reportService): void
    {
        $start = $this->period->getStartDate();
        $end = $this->period->getEndDate();

        $results = $reportService->getResults($start, $end);

        if ($results->isEmpty()) {
            return;
        }

        $stats = $reportService->calculateStats($results);
        $serverStats = $reportService->calculateServerStats($results);

        $periodName = $this->period->getName();
        $periodLabel = $this->period->getLabel();

        // Send mail notifications
        if ($this->period->isEnabledForMail($settings)) {
            $this->sendMailNotifications($settings, $stats, $periodName, $periodLabel, $serverStats);
        }

        // Send Apprise notifications
        if ($this->period->isEnabledForApprise($settings)) {
            $this->sendAppriseNotifications($settings, $stats, $periodName, $periodLabel, $serverStats);
        }

        // Send webhook notifications
        if ($this->period->isEnabledForWebhook($settings)) {
            $this->sendWebhookNotifications($settings, $start, $end, $stats, $periodName, $periodLabel, $serverStats);
        }
    }
}
