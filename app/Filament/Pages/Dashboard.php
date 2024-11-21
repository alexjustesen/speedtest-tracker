<?php

namespace App\Filament\Pages;

use App\Actions\Ookla\StartSpeedtest;
use App\Filament\Widgets\RecentDownloadChartWidget;
use App\Filament\Widgets\RecentDownloadLatencyChartWidget;
use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Filament\Widgets\RecentUploadLatencyChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BasePage;
use Filament\Support\Enums\IconPosition;

class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.dashboard';

    public function getSubheading(): ?string
    {
        $schedule = config('speedtest.schedule');

        if (blank($schedule) || $schedule === false) {
            return __('No speedtests scheduled.');
        }

        $cronExpression = new CronExpression($schedule);

        $nextRunDate = Carbon::parse($cronExpression->getNextRunDate(timeZone: config('app.display_timezone')))->format(config('app.datetime_format'));

        return 'Next speedtest at: '.$nextRunDate;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('home')
                ->label('Public Dashboard')
                ->icon('heroicon-o-chart-bar')
                ->iconPosition(IconPosition::Before)
                ->color('gray')
                ->hidden(fn (): bool => ! config('speedtest.public_dashboard'))
                ->url(shouldOpenInNewTab: true, url: '/'),
            ActionGroup::make([
                Action::make('ookla speedtest')
                    ->action(function () {
                        StartSpeedtest::run();

                        Notification::make()
                            ->title('Ookla speedtest started')
                            ->success()
                            ->send();
                    }),
            ])
                ->button()
                ->color('primary')
                ->dropdownPlacement('bottom-end')
                ->label('Run Speedtest')
                ->icon('heroicon-o-rocket-launch')
                ->iconPosition(IconPosition::Before)
                ->hidden(! auth()->user()->is_admin),
        ];
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
