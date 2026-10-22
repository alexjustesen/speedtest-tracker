<?php

namespace App\Livewire\Topbar;

use App\Actions\GetOoklaSpeedtestServers;
use App\Actions\Speedtests\RunSpeedtest;
use App\Enums\ResultService;
use App\Helpers\Ookla;
use App\Rules\Iperf3Server;
use App\Settings\SpeedtestSettings;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Actions extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public bool $showDashboard = true;

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
                Select::make('service')
                    ->label(__('results.service'))
                    ->options(ResultService::selectable())
                    ->default(fn (): string => array_key_exists(app(SpeedtestSettings::class)->default_service, ResultService::selectable())
                        ? app(SpeedtestSettings::class)->default_service
                        : ResultService::Ookla->value)
                    ->selectablePlaceholder(false)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('target', ResultService::tryFrom((string) $state)?->configuredTargets()[0] ?? null)),
                Select::make('server_id')
                    ->label(__('results.select_server'))
                    ->helperText(__('results.select_server_helper'))
                    ->options(function (): array {
                        return array_filter([
                            __('results.manual_servers') => Ookla::getConfigServers(),
                            __('results.closest_servers') => GetOoklaSpeedtestServers::run(),
                        ]);
                    })
                    ->searchable()
                    ->visible(fn (Get $get): bool => self::selectedService($get) === ResultService::Ookla),
                TextInput::make('target')
                    ->label(fn (Get $get): string => self::selectedService($get) === ResultService::File
                        ? __('results.file_url')
                        : __('results.iperf3_server'))
                    ->helperText(fn (Get $get): string => self::selectedService($get) === ResultService::File
                        ? __('results.file_url_helper')
                        : __('results.iperf3_server_helper'))
                    ->placeholder(fn (Get $get): string => self::selectedService($get) === ResultService::File
                        ? 'https://example.com/100MB.bin'
                        : 'iperf.example.com:5201')
                    ->datalist(fn (Get $get): array => self::targetSuggestions(self::selectedService($get)))
                    ->default(fn (Get $get): ?string => self::selectedService($get)?->configuredTargets()[0] ?? null)
                    ->required(fn (Get $get): bool => (bool) self::selectedService($get)?->requiresTarget())
                    ->rules(fn (Get $get): array => self::targetRules(self::selectedService($get)))
                    ->visible(fn (Get $get): bool => (bool) self::selectedService($get)?->requiresTarget()),
            ])
            ->action(function (array $data) {
                $service = ResultService::tryFrom($data['service'] ?? '') ?? ResultService::Ookla;

                RunSpeedtest::run(
                    service: $service,
                    target: $service === ResultService::Ookla
                        ? ($data['server_id'] ?? null)
                        : ($data['target'] ?? null),
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
            ->size(request()->is('filament*') ? Size::Medium : Size::Large)
            ->color('primary')
            ->label(__('results.speedtest'))
            ->icon('tabler-rocket')
            ->iconPosition(IconPosition::Before)
            ->hidden(! Auth::check() && Auth::user()->is_admin)
            ->extraAttributes([
                'id' => 'speedtestAction',
            ]);
    }

    protected static function selectedService(Get $get): ?ResultService
    {
        $service = $get('service');

        return $service instanceof ResultService ? $service : ResultService::tryFrom((string) $service);
    }

    /**
     * Configured targets first, then a few well-known public ones.
     */
    protected static function targetSuggestions(?ResultService $service): array
    {
        $public = match ($service) {
            ResultService::Iperf3 => [
                'ping.online.net:5200',
                'bouygues.iperf.fr:5200',
                'speedtest.serverius.net:5002',
                'iperf.he.net',
            ],
            ResultService::File => [
                'https://speedtest.selectel.ru/100MB',
                'https://fsn1-speed.hetzner.com/100MB.bin',
                'https://hel1-speed.hetzner.com/100MB.bin',
            ],
            default => [],
        };

        return array_values(array_unique([...($service?->configuredTargets() ?? []), ...$public]));
    }

    protected static function targetRules(?ResultService $service): array
    {
        return match ($service) {
            ResultService::File => ['url:http,https'],
            ResultService::Iperf3 => [new Iperf3Server],
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.topbar.actions');
    }
}
