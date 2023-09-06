<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentSpeedChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Jobs\ExecSpeedtest;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    public ?Result $latestResult = null;

    public string $lastResult = 'never';

    protected static string $view = 'filament.pages.dashboard';

    public function mount()
    {
        $this->latestResult = Result::query()
            ->latest()
            ->first();

        if ($this->latestResult) {
            $settings = new GeneralSettings();

            $this->lastResult = $this->latestResult->created_at
                ->timezone($settings->timezone)
                ->format($settings->time_format);
        }
    }

    public function getMaxContentWidth(): string
    {
        $settings = new GeneralSettings();

        return $settings->content_width;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('speedtest')
                ->label('Queue Speedtest')
                ->action('queueSpeedtest'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::make([
                'result' => $this->latestResult,
            ]),
        ];
    }

    protected function getFooterWidgets(): array
    {
        if (! $this->latestResult) {
            return [];
        }

        return [
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
