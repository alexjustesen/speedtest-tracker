<?php

namespace App\Filament\Pages;

use App\Actions\Speedtests\RunOoklaSpeedtest;
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
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BasePage;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Http;

class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.dashboard';

    public function getSubheading(): ?string
    {
        if (blank(config('speedtest.schedule'))) {
            return __('No speedtests scheduled.');
        }

        $cronExpression = new CronExpression(config('speedtest.schedule'));

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
            Action::make('ookla_speedtest')
                ->form([
                    Forms\Components\Select::make('server_id')
                        ->label('Select Server')
                        ->options(fn (callable $get) => $this->getServerSearchOptions($get('server_search')))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $serverId = $data['server_id'];
                    RunOoklaSpeedtest::run(serverId: $serverId);

                    Notification::make()
                        ->title('Ookla speedtest started')
                        ->success()
                        ->send();
                })
                ->modalHeading('Run Speedtest')
                ->modalButton('Run Speedtest')
                ->modalWidth('lg')
                ->button()
                ->color('primary')
                ->label('Run Speedtest')
                ->icon('heroicon-o-rocket-launch')
                ->iconPosition(IconPosition::Before)
                ->hidden(! auth()->user()->is_admin),
        ];
    }

    protected function getServerSearchOptions(?string $search = ''): array
    {
        // Make an API request to fetch the servers
        $response = Http::get('https://www.speedtest.net/api/js/servers', [
            'engine' => 'js',
            'search' => $search ?? '',
            'https_functional' => true,
            'limit' => 20,
        ]);

        // Check if the response failed
        if ($response->failed()) {
            return ['' => 'Error retrieving Speedtest servers'];
        }

        // Get the JSON response
        $servers = $response->json();

        // Ensure that the response is an array
        if (! is_array($servers)) {
            return ['' => 'Invalid response format'];
        }

        // Map the server options, ensuring each item is valid
        return collect($servers)->mapWithKeys(function ($item) {
            if (is_array($item) && isset($item['id'], $item['name'], $item['sponsor'])) {
                return [$item['id'] => "{$item['name']} ({$item['sponsor']})"];
            }

            return [];

        })->toArray();
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
