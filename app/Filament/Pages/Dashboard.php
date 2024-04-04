<?php

namespace App\Filament\Pages;

use App\Actions\Speedtests\RunOoklaSpeedtest;
use App\Filament\Widgets\RecentDownloadChartWidget;
use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\RecentUploadLatencyChartWidget;
use App\Filament\Widgets\RecentDownloadLatencyChartWidget;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BasePage;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Arr;

class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.dashboard';

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
                    ->action(function (GeneralSettings $settings) {
                        $serverId = null;

                        if (is_array($settings->speedtest_server) && count($settings->speedtest_server)) {
                            $serverId = Arr::random($settings->speedtest_server);
                        }

                        RunOoklaSpeedtest::run(serverId: $serverId);

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
            RecentUploadLatencyChartWidget::make(),
            RecentDownloadLatencyChartWidget::make(),
        ];
    }
}
