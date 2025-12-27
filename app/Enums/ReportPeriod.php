<?php

namespace App\Enums;

use Carbon\Carbon;

enum ReportPeriod: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';

    public function getStartDate(): Carbon
    {
        return match ($this) {
            self::Daily => now()->subDay()->startOfDay(),
            self::Weekly => now()->subWeek()->startOfWeek(),
            self::Monthly => now()->subMonth()->startOfMonth(),
        };
    }

    public function getEndDate(): Carbon
    {
        return match ($this) {
            self::Daily => now()->subDay()->endOfDay(),
            self::Weekly => now()->subWeek()->endOfWeek(),
            self::Monthly => now()->subMonth()->endOfMonth(),
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Daily => now()->subDay()->format('F j, Y'),
            self::Weekly => $this->getStartDate()->format('M j').' - '.$this->getEndDate()->format('M j, Y'),
            self::Monthly => $this->getStartDate()->format('F Y'),
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::Daily => 'Daily',
            self::Weekly => 'Weekly',
            self::Monthly => 'Monthly',
        };
    }

    public function isEnabledForMail($settings): bool
    {
        return match ($this) {
            self::Daily => $settings->mail_enabled && $settings->mail_daily_average_enabled,
            self::Weekly => $settings->mail_enabled && $settings->mail_weekly_average_enabled,
            self::Monthly => $settings->mail_enabled && $settings->mail_monthly_average_enabled,
        };
    }

    public function isEnabledForApprise($settings): bool
    {
        return match ($this) {
            self::Daily => $settings->apprise_enabled && $settings->apprise_daily_average_enabled,
            self::Weekly => $settings->apprise_enabled && $settings->apprise_weekly_average_enabled,
            self::Monthly => $settings->apprise_enabled && $settings->apprise_monthly_average_enabled,
        };
    }

    public function isEnabledForWebhook($settings): bool
    {
        return match ($this) {
            self::Daily => $settings->webhook_enabled && $settings->webhook_daily_average_enabled,
            self::Weekly => $settings->webhook_enabled && $settings->webhook_weekly_average_enabled,
            self::Monthly => $settings->webhook_enabled && $settings->webhook_monthly_average_enabled,
        };
    }

    public function isAnyChannelEnabled($settings): bool
    {
        return $this->isEnabledForMail($settings)
            || $this->isEnabledForApprise($settings)
            || $this->isEnabledForWebhook($settings);
    }
}
