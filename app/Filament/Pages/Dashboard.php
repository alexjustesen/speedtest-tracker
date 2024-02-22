<?php

namespace App\Filament\Pages;

use App\Console\Commands\RunOoklaSpeedtest;
use App\Filament\Widgets\RecentDownloadChartWidget;
use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BasePage;
use Illuminate\Support\Facades\Artisan;

class Dashboard extends BasePage
{
    public bool $publicDashboard = false;

    protected static ?string $pollingInterval = null;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dashboard';

    public function mount()
    {
        $settings = new GeneralSettings();

        $this->publicDashboard = $settings->public_dashboard_enabled;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('home')
                ->label('Public Dashboard')
                ->color('gray')
                ->hidden(! $this->publicDashboard)
                ->url('/'),
            Action::make('speedtest')
                ->label('Queue Speedtest')
                ->color('primary')
                ->action('queueSpeedtest')
                ->hidden(fn (): bool => ! auth()->user()->is_admin && ! auth()->user()->is_user),
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

    public function queueSpeedtest(): void
    {
        try {
            Artisan::call(RunOoklaSpeedtest::class);
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Manual speedtest failed!')
                ->body('The starting a manual speedtest failed, check the logs.')
                ->warning()
                ->sendToDatabase(auth()->user());

            return;
        }

        Notification::make()
            ->title('Speedtest added to the queue')
            ->success()
            ->send();
    }
}
