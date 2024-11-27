<?php

namespace App\Livewire\Topbar;

use App\Actions\GetOoklaSpeedtestServers;
use App\Actions\Ookla\StartSpeedtest;
use App\Helpers\Ookla;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Livewire\Component;

class RunSpeedtestAction extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public function dashboardAction(): Action
    {
        return Action::make('home')
            ->label('Dashboard')
            ->icon('heroicon-o-chart-bar')
            ->iconPosition(IconPosition::Before)
            ->color('gray')
            ->hidden(fn (): bool => ! config('speedtest.public_dashboard'))
            ->url(shouldOpenInNewTab: true, url: '/')
            ->extraAttributes([
                'id' => 'dashboardAction',
            ]);
    }

    public function speedtestAction(): Action
    {
        return Action::make('speedtest')
            ->form([
                Select::make('server_id')
                    ->label('Select Server')
                    ->helperText('Leave empty to run the speedtest without specifying a server.')
                    ->options(function (): array {
                        return array_filter([
                            'Manual servers' => Ookla::getConfigServers(),
                            'Closest servers' => GetOoklaSpeedtestServers::run(),
                        ]);
                    })
                    ->searchable(),
            ])
            ->action(function (array $data) {
                $serverId = $data['server_id'] ?? null;

                StartSpeedtest::run(
                    scheduled: false,
                    serverId: $serverId,
                );

                Notification::make()
                    ->title('Speedtest started')
                    ->success()
                    ->send();
            })
            ->modalHeading('Run Speedtest')
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Start')
            ->button()
            ->color('primary')
            ->label('Speedtest')
            ->icon('heroicon-o-rocket-launch')
            ->iconPosition(IconPosition::Before)
            ->hidden(! auth()->user()->is_admin)
            ->extraAttributes([
                'id' => 'speedtestAction',
            ]);
    }

    public function render()
    {
        return view('livewire.topbar.run-speedtest-action');
    }
}
