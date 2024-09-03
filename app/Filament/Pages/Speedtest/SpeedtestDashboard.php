<?php

namespace App\Filament\Pages\Speedtest;

use App\Filament\Widgets\Speedtest\RecentDownloadChartWidget;
use App\Filament\Widgets\Speedtest\RecentDownloadLatencyChartWidget;
use App\Filament\Widgets\Speedtest\RecentJitterChartWidget;
use App\Filament\Widgets\Speedtest\RecentPingChartWidget;
use App\Filament\Widgets\Speedtest\RecentUploadChartWidget;
use App\Filament\Widgets\Speedtest\RecentUploadLatencyChartWidget;
use Filament\Pages\Page;

class SpeedtestDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static string $view = 'filament.pages.speedtest-dashboard';

    protected static ?string $navigationGroup = 'Speedtest';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Overview';

    protected function getHeaderWidgets(): array
    {
        return [
            RecentDownloadChartWidget::make(),
            RecentUploadChartWidget::make(),
            RecentPingChartWidget::make(),
            RecentJitterChartWidget::make(),
            RecentDownloadLatencyChartWidget::make(),
            RecentUploadLatencyChartWidget::make(),
        ];
    }
}
