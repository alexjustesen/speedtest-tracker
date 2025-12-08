<?php

namespace App\Actions\Notifications\Average;

use App\Enums\ReportPeriod;
use App\Jobs\Notifications\SendPeriodicAverageReportJob;
use App\Settings\NotificationSettings;

class CheckAndSendPeriodicAverageNotifications
{
    public static function run(ReportPeriod $period): void
    {
        $notificationSettings = app(NotificationSettings::class);

        if ($period->isAnyChannelEnabled($notificationSettings)) {
            SendPeriodicAverageReportJob::dispatch($period);
        }
    }
}
