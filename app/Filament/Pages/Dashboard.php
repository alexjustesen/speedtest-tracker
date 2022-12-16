<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentPingChart;
use App\Filament\Widgets\RecentSpeedChart;
use App\Filament\Widgets\StatsOverview;
use App\Jobs\ExecSpeedtest;
use App\Settings\GeneralSettings;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static string $view = 'filament.pages.dashboard';

    protected function getActions(): array
    {
        return [
            Action::make('speedtest')
                ->label('Queue Speedtest')
                ->action('queueSpeedtest'),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            RecentSpeedChart::class,
            RecentPingChart::class,
        ];
    }

    public function queueSpeedtest(GeneralSettings $settings)
    {
        $speedtest = [
            'enabled' => ! blank($settings->speedtest_schedule),
            'schedule' => optional($settings)->speedtest_schedule,
            'ookla_server_id' => optional($settings)->speedtest_server,
        ];

        ExecSpeedtest::dispatch(speedtest: $speedtest, scheduled: false);

        Notification::make()
            ->title('Speedtest added to the queue')
            ->success()
            ->send();
    }
}
