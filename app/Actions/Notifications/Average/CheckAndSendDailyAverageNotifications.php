<?php

namespace App\Actions\Notifications\Average;

use App\Enums\ReportPeriod;
use App\Jobs\Notifications\SendPeriodicAverageReportJob;
use App\Settings\NotificationSettings;

class CheckAndSendDailyAverageNotifications
{
    public static function run(): void
    {
        $notificationSettings = app(NotificationSettings::class);
        $period = ReportPeriod::Daily;

        if ($period->isAnyChannelEnabled($notificationSettings)) {
            SendPeriodicAverageReportJob::dispatch($period);
        }
    }
}
