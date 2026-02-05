<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ScheduleStatus: string implements HasColor, HasLabel
{
    case Healthy = 'healthy';
    case Unhealthy = 'unhealthy';
    case Failed = 'failed';
    case NotTested = 'not_tested';

    public function getColor(): ?string
    {
        return match ($this) {
            self::Healthy => 'success',
            self::Unhealthy => 'warning',
            self::Failed => 'danger',
            self::NotTested => 'gray',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Healthy => __('enums.schedule_status.healthy'),
            self::Unhealthy => __('enums.schedule_status.unhealthy'),
            self::Failed => __('enums.schedule_status.failed'),
            self::NotTested => __('enums.schedule_status.not_tested'),
        };
    }
}
