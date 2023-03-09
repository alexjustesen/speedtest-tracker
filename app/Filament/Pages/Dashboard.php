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
use Illuminate\Contracts\View\View;

class Dashboard extends BasePage
{
    public string $lastResult = 'never';

    public int $resultsCount;

    protected static string $view = 'filament.pages.dashboard';

    public function render(): View
    {
        $this->resultsCount = Result::count();

        if ($this->resultsCount) {
            $result = Result::latest()
                ->first();

            $settings = new GeneralSettings();

            $this->lastResult = $result->created_at
                    ->timezone($settings->timezone)
                    ->format($settings->time_format);
        }

        return view(static::$view, $this->getViewData())
            ->layout(static::$layout, $this->getLayoutData());
    }

    protected function getMaxContentWidth(): string
    {
        $settings = new GeneralSettings();

        return $settings->content_width;
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
        if (! $this->resultsCount) {
            return [];
        }

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
            'ookla_server_id' => $ookla_server_id,
        ];

        ExecSpeedtest::dispatch(speedtest: $speedtest, scheduled: false);

        Notification::make()
            ->title('Speedtest added to the queue')
            ->success()
            ->send();
    }
}
