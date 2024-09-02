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
use App\Forms\Components\DateFilterForm;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Arr;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public function getSubheading(): ?string
    {
        if (blank(config('speedtest.schedule'))) {
            return __('No speedtests scheduled.');
        }

        $cronExpression = new CronExpression(config('speedtest.schedule'));

        $nextRunDate = Carbon::parse($cronExpression->getNextRunDate(timeZone: config('app.display_timezone')))->format(config('app.datetime_format'));

        return 'Next speedtest at: '.$nextRunDate;
    }

    public function filtersForm(Form $form): Form
    {
        return DateFilterForm::make($form);
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
                        $servers = array_filter(
                            explode(',', config('speedtest.servers'))
                        );

                        $serverId = null;

                        if (count($servers)) {
                            $serverId = Arr::random($servers);
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
        ];
    }

    public function getWidgets(): array
    {
        return [
            RecentDownloadChartWidget::make(),
            RecentUploadChartWidget::make(),
            RecentPingChartWidget::make(),
            RecentJitterChartWidget::make(),
            RecentDownloadLatencyChartWidget::make(),
            RecentUploadLatencyChartWidget::make(),
        ];
    }
}
