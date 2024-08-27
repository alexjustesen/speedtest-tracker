<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Latency\RecentLatencyChartWidget;
use App\Models\PingResult;
use Filament\Pages\Page;

class Latency extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.ping-results-page';

    protected static ?string $navigationGroup = 'Latency';

    protected static ?string $navigationLabel = 'Overview';

    public function getData()
    {
        // Retrieve distinct URLs
        $urls = PingResult::distinct()->pluck('url');

        return [
            'urls' => $urls,
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
        $urls = $this->getData()['urls'];

        $widgets = [];
        foreach ($urls as $url) {
            $widget = RecentLatencyChartWidget::make(['url' => $url]); // Pass URL during creation
            $widgets[] = $widget;
        }

        return $widgets;
    }
}
