<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentDownloadChartWidget;
use App\Filament\Widgets\RecentDownloadLatencyChartWidget;
use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Filament\Widgets\RecentUploadLatencyChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.dashboard';

    public function getSubheading(): ?string
    {
        $nextRunAt = Schedule::query()
            ->where('is_active', true)
            ->whereNotNull('next_run_at')
            ->orderBy('next_run_at')
            ->value('next_run_at');

        if (blank($nextRunAt)) {
            return __('No Active schedules.');
        }

        $formatted = Carbon::parse($nextRunAt)
            ->timezone(config('app.display_timezone'))
            ->format(config('app.datetime_format'));

        return 'Next speedtest at: '.$formatted;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::make(),
            RecentDownloadChartWidget::make(),
            RecentUploadChartWidget::make(),
            RecentPingChartWidget::make(),
            RecentJitterChartWidget::make(),
            RecentDownloadLatencyChartWidget::make(),
            RecentUploadLatencyChartWidget::make(),
        ];
    }
}
