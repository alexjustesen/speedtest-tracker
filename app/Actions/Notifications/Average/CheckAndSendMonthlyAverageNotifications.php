<?php

namespace App\Actions\Notifications\Average;

use App\Jobs\Notifications\SendMonthlyAverageReportJob;
use App\Settings\NotificationSettings;

class CheckAndSendMonthlyAverageNotifications
{
    public static function run(): void
    {
        $notificationSettings = app(NotificationSettings::class);

        if ($notificationSettings->mail_monthly_average_enabled) {
            SendMonthlyAverageReportJob::dispatch();
        }
    }
}
