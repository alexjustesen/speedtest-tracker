<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentJitterChart;
use App\Filament\Widgets\RecentPingChart;
use App\Filament\Widgets\RecentSpeedChart;
use App\Filament\Widgets\StatsOverview;
use App\Jobs\ExecSpeedtest;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    public string $lastResult;

    protected static string $view = 'filament.pages.dashboard';

    public function mount()
    {
        $result = Result::latest()
            ->first();

        $settings = new GeneralSettings();

        $this->lastResult = $result
            ? $result->created_at->timezone($settings->timezone)->format($settings->time_format)
            : 'never';
    }

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
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            RecentSpeedChart::class,
            RecentPingChart::class,
            RecentJitterChart::class,
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
            'enabled' => ! blank($settings->speedtest_schedule),
            'schedule' => optional($settings)->speedtest_schedule,
            'ookla_server_id' => $ookla_server_id,
        ];

        ExecSpeedtest::dispatch(speedtest: $speedtest, scheduled: false);

        Notification::make()
            ->title('Speedtest added to the queue')
            ->success()
            ->send();
    }
}
