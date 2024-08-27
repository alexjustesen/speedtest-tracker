<?php

namespace App\Filament\Pages;

use App\Models\PingResult;
use App\Filament\Widgets\Latency\RecentLatencyChartWidget;
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
        \Log::info("Retrieved URLs: ", $urls->toArray());

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

        \Log::info("Creating widgets for URLs: ", $urls->toArray());

        $widgets = [];
        foreach ($urls as $url) {
            $widget = RecentLatencyChartWidget::make(['url' => $url]); // Pass URL during creation
           # \Log::info("Assigning URL to widget: " . $url . " - Widget URL: " . $widget->url);
            $widgets[] = $widget;
        }

        return $widgets;
    }
}