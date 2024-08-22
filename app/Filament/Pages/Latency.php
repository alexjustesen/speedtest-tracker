<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentLatencyChartWidget;
use Filament\Pages\Page;

class Latency extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.ping-results-page';

    public function getData()
    {
        return [
            'filters' => $this->getFilters(),
        ];
    }

    protected function getFilters(): array
    {
        return [
            '24h' => 'Last 24h',
            'week' => 'Last week',
            'month' => 'Last month',
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RecentLatencyChartWidget::make(),
        ];
    }
}
