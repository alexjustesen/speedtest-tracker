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
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Actions extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public function dashboardAction(): Action
    {
        return Action::make('metrics')
            ->iconButton()
            ->icon('tabler-chart-histogram')
            ->color('gray')
            ->url(url: route('home'))
            ->extraAttributes([
                'id' => 'dashboardAction',
            ]);
    }

    public function speedtestAction(): Action
    {
        return Action::make('speedtest')
            ->schema([
                Select::make('server_id')
                    ->label(__('results.select_server'))
                    ->helperText(__('results.select_server_helper'))
                    ->options(function (): array {
                        return array_filter([
                            __('results.manual_servers') => Ookla::getConfigServers(),
                            __('results.closest_servers') => GetOoklaSpeedtestServers::run(),
                        ]);
                    })
                    ->searchable(),
            ])
            ->action(function (array $data) {
                $serverId = $data['server_id'] ?? null;

                RunSpeedtest::run(
                    serverId: $serverId,
                    dispatchedBy: Auth::id(),
                );

                Notification::make()
                    ->title(__('results.speedtest_started'))
                    ->success()
                    ->send();
            })
            ->modalHeading(__('results.speedtest'))
            ->modalWidth('lg')
            ->modalSubmitActionLabel(__('results.start'))
            ->button()
            ->size(Size::Medium)
            ->color('primary')
            ->label(__('results.speedtest'))
            ->icon('tabler-rocket')
            ->iconPosition(IconPosition::Before)
            ->hidden(! Auth::check() && Auth::user()->is_admin)
            ->extraAttributes([
                'id' => 'speedtestAction',
            ]);
    }

    public function render()
    {
        return view('livewire.topbar.actions');
    }
}
