<?php

namespace App\Filament\Pages\Speedtest;

use App\Filament\Widgets\Speedtest\Insights\AverageDownloadUploadChartWidget;
use App\Filament\Widgets\Speedtest\Insights\ResultStatusWidget;
use App\Filament\Widgets\Speedtest\Insights\ResultThresholdWidget;
use Filament\Pages\Page;

class InsightsCharts extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.pages.speedtest-insights';

    protected static ?string $navigationGroup = 'Speedtest';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Insights';

    protected static ?string $title = 'Insights';

    protected function getHeaderWidgets(): array
    {
        return [
            ResultStatusWidget::make(),
            ResultThresholdWidget::make(),
            AverageDownloadUploadChartWidget::make(),
        ];
    }
}
