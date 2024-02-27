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
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 1;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('home')
                ->label('Public Dashboard')
                ->color('gray')
                ->hidden(function (GeneralSettings $settings): bool {
                    return ! $settings->public_dashboard_enabled;
                })
                ->url('/'),
            Action::make('speedtest')
                ->label('Queue Speedtest')
                ->color('primary')
                ->action('queueSpeedtest')
                ->hidden(fn (): bool => ! Auth::user()->is_admin),
        ];
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\DateTimePicker::make('startDate')
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('endDate')
                            ->seconds(false)
                            ->maxDate(now()),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ]);
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
