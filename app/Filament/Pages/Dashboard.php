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
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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
        // Retrieve the default number of days from the configuration
        $defaultRangeDays = config('app.chart_time_range');

        // Calculate the start and end dates based on the configuration value
        $defaultEndDate = now(); // Today
        $defaultStartDate = now()->subDays($defaultRangeDays); // Start date for the range

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Select::make('predefinedRange')
                            ->label('Time Range')
                            ->options([
                                '3_hours' => 'Last 3 Hours',
                                '6_hours' => 'Last 6 Hours',
                                '12_hours' => 'Last 12 Hours',
                                '24_hours' => 'Last 24 Hours',
                                '1_week' => 'Last 1 Week',
                                '1_month' => 'Last 1 Month',
                                '3_months' => 'Last 3 Months',
                                '6_months' => 'Last 6 Months',
                                'custom' => 'Custom Range',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                switch ($state) {
                                    case '3_hours':
                                        $set('startDate', now()->subHours(3)->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case '6_hours':
                                        $set('startDate', now()->subHours(6)->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case '12_hours':
                                        $set('startDate', now()->subHours(12)->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case '24_hours':
                                        $set('startDate', now()->subDay()->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case '1_week':
                                        $set('startDate', now()->subWeek()->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case '1_month':
                                        $set('startDate', now()->subMonth()->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case '3_months':
                                        $set('startDate', now()->subMonths(3)->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case '6_months':
                                        $set('startDate', now()->subMonths(6)->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case 'custom':
                                        break;
                                }
                            })
                            ->default('custom'),

                        DatePicker::make('startDate')
                            ->label('Start Date')
                            ->default($defaultStartDate->toDateString())
                            ->reactive()
                            ->hidden(fn ($get) => $get('predefinedRange') !== 'custom'),

                        DatePicker::make('endDate')
                            ->label('End Date')
                            ->default($defaultEndDate->toDateString())
                            ->reactive()
                            ->hidden(fn ($get) => $get('predefinedRange') !== 'custom'),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 3,
                    ]),
            ]);
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
