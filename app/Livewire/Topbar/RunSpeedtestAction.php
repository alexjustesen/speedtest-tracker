<?php

namespace App\Livewire\Topbar;

use App\Actions\GetOoklaSpeedtestServers;
use App\Actions\Ookla\RunSpeedtest;
use App\Helpers\Ookla;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RunSpeedtestAction extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public function dashboardAction(): Action
    {
        return Action::make('home')
            ->label(__('translations.speedtest.public_dashboard'))
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
                    ->label(__('translations.speedtest.select_server'))
                    ->helperText(__('translations.speedtest.server_helper_text'))
                    ->options(function (): array {
                        return array_filter([
                            __('translations.speedtest.manual_servers') => Ookla::getConfigServers(),
                            __('translations.speedtest.closest_servers') => GetOoklaSpeedtestServers::run(),
                        ]);
                    })
                    ->searchable(),
            ])
            ->action(function (array $data) {
                $serverId = $data['server_id'] ?? null;

                RunSpeedtest::run(
                    serverId: $serverId,
                );

                Notification::make()
                    ->title(__('translations.speedtest.speedtest_started'))
                    ->success()
                    ->send();
            })
            ->modalHeading(__('translations.speedtest.run_speedtest'))
            ->modalWidth('lg')
            ->modalSubmitActionLabel(__('translations.speedtest.start'))
            ->button()
            ->color('primary')
            ->label(__('translations.speedtest.speedtest'))
            ->icon('heroicon-o-rocket-launch')
            ->iconPosition(IconPosition::Before)
            ->hidden(! Auth::check() && Auth::user()->is_admin)
            ->extraAttributes([
                'id' => 'speedtestAction',
            ]);
    }

    public function render()
    {
        return view('livewire.topbar.run-speedtest-action');
    }
}
