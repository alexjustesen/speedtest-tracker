<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Jobs\ExecSpeedtest;
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
        ];
    }

    public function queueSpeedtest()
    {
        ExecSpeedtest::dispatch();

        Notification::make()
            ->title('Speedtest added to the queue')
            ->success()
            ->send();
    }
}
