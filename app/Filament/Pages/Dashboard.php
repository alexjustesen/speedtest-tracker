<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentSpeedChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Jobs\ExecSpeedtest;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BasePage;

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
            RecentSpeedChartWidget::make(),
            RecentPingChartWidget::make(),
            RecentJitterChartWidget::make(),
        ];
    }

    public function queueSpeedtest(GeneralSettings $settings)
    {
        $ookla_server_id = null;

        if (! blank($settings->speedtest_server)) {
            $item = array_rand($settings->speedtest_server);

            $ookla_server_id = $settings->speedtest_server[$item];
        }

        $speedtest = [
            'ookla_server_id' => $ookla_server_id,
        ];

        ExecSpeedtest::dispatch(speedtest: $speedtest, scheduled: false);

        Notification::make()
            ->title('Speedtest added to the queue')
            ->success()
            ->send();
    }
}
