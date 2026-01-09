<?php

namespace App\Actions\Notifications\Average;

use App\Enums\ReportPeriod;
use App\Jobs\Notifications\SendPeriodicAverageReportJob;
use App\Settings\NotificationSettings;

class CheckAndSendPeriodicAverageNotifications
{
    public static function run(ReportPeriod $period): void
    {
        $settings = new NotificationSettings;

        $mailEnabled = match ($period) {
            ReportPeriod::Daily => $settings->mail_enabled && $settings->mail_daily_average_enabled,
            ReportPeriod::Weekly => $settings->mail_enabled && $settings->mail_weekly_average_enabled,
            ReportPeriod::Monthly => $settings->mail_enabled && $settings->mail_monthly_average_enabled,
        };

        $appriseEnabled = match ($period) {
            ReportPeriod::Daily => $settings->apprise_enabled && $settings->apprise_daily_average_enabled,
            ReportPeriod::Weekly => $settings->apprise_enabled && $settings->apprise_weekly_average_enabled,
            ReportPeriod::Monthly => $settings->apprise_enabled && $settings->apprise_monthly_average_enabled,
        };

        if (! $mailEnabled && ! $appriseEnabled) {
            return;
        }

        SendPeriodicAverageReportJob::dispatch($period, $mailEnabled, $appriseEnabled);
    }
}
