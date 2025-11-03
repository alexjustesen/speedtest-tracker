<?php

namespace App\Filament\Widgets\Concerns;

trait HasChartFilters
{
    protected function getFilters(): ?array
    {
        return [
            '24h' => 'Last 24 hours',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
        ];
    }
}
