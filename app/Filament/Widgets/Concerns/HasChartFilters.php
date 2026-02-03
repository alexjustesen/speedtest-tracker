<?php

namespace App\Filament\Widgets\Concerns;

trait HasChartFilters
{
    protected function getFilters(): ?array
    {
        return [
            '24h' => __('general.last_24h'),
            'week' => __('general.last_week'),
            'month' => __('general.last_month'),
        ];
    }
}
