<?php

namespace App\Filament\Pages;

use App\Actions\Speedtests\RunOoklaSpeedtest;
use App\Filament\Widgets\RecentDownloadChartWidget;
use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BasePage;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;

class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('home')
                ->label('Public Dashboard')
                ->color('gray')
                ->hidden(fn (GeneralSettings $settings): bool => ! $settings->public_dashboard_enabled)
                ->url('/'),
            ActionGroup::make([
                Action::make('ookla speedtest')
                    ->action(function () {
                        RunOoklaSpeedtest::run();

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
                ->iconPosition(IconPosition::After)
                ->hidden(! auth()->user()->is_admin)
                ->size(ActionSize::Small),
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
        ];
    }
}
