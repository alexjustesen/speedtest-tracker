<?php

namespace App\Filament\Resources\Speedtests\Schemas;

use App\Actions\GetOoklaSpeedtestServers;
use App\Actions\Schedules\ExplainCronExpression;
use App\Models\Speedtest;
use App\Rules\Cron;
use App\Rules\IpOrCidr;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class SpeedtestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('schedules.tab_general'))
                            ->icon('tabler-clock')
                            ->columns(2)
                            ->schema([
                                Toggle::make('enabled')
                                    ->label(__('schedules.enabled'))
                                    ->default(true)
                                    ->afterStateHydrated(fn (Toggle $component, ?Speedtest $record) => $component->state($record?->isEnabled ?? true))
                                    ->columnSpanFull(),

                                TextInput::make('name')
                                    ->label(__('schedules.name'))
                                    ->placeholder(__('schedules.name_placeholder'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('schedule')
                                    ->label(__('schedules.schedule'))
                                    ->placeholder(__('schedules.schedule_placeholder'))
                                    ->hintAction(
                                        Action::make('crontab_guru')
                                            ->label('crontab.guru')
                                            ->icon('tabler-external-link')
                                            ->url('https://crontab.guru', shouldOpenInNewTab: true)
                                    )
                                    ->helperText(fn (Get $get) => ExplainCronExpression::run($get('schedule')))
                                    ->live(debounce: 500)
                                    ->afterStateHydrated(fn (TextInput $component, ?Speedtest $record) => $component->state($record?->cronExpression))
                                    ->validationMessages(['unique' => __('schedules.schedule_overlap')])
                                    ->rules(fn (?Speedtest $record) => [
                                        new Cron,
                                        Rule::unique('schedules', 'expression')
                                            ->where('type', 'cron')
                                            ->ignore($record?->cronSchedule()?->id),
                                    ])
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Tab::make(__('schedules.tab_servers'))
                            ->icon('tabler-server')
                            ->schema([
                                Radio::make('server_mode')
                                    ->label(__('schedules.server_mode'))
                                    ->options(__('schedules.server_mode_options'))
                                    ->default('auto')
                                    ->live()
                                    ->afterStateHydrated(function (Radio $component, Get $get) {
                                        if (! blank($get('servers'))) {
                                            $component->state('prefer');
                                        } elseif (! blank($get('blocked_servers'))) {
                                            $component->state('block');
                                        } else {
                                            $component->state('auto');
                                        }
                                    }),

                                self::ServerSelection('servers', 'prefer'),
                                self::ServerSelection('blocked_servers', 'block'),
                            ]),

                        Tab::make(__('schedules.tab_network'))
                            ->icon('tabler-network')
                            ->columns(2)
                            ->schema([
                                TextInput::make('interface')
                                    ->label(__('schedules.interface'))
                                    ->helperText(__('schedules.interface_helper'))
                                    ->placeholder('eth0')
                                    ->maxLength(255)
                                    ->nullable(),

                                TagsInput::make('skip_ips')
                                    ->label(__('schedules.skip_ips'))
                                    ->helperText(__('schedules.skip_ips_helper'))
                                    ->placeholder('1.1.1.1')
                                    ->splitKeys(['Tab', ' ', ','])
                                    ->rules([new IpOrCidr])
                                    ->dehydrateStateUsing(fn (?array $state): ?array => blank($state) ? null : $state),
                            ]),
                    ]),
            ]);
    }

    private static function ServerSelection(string $fieldName, string $mode): Select
    {
        return Select::make($fieldName)
            ->label(__("schedules.{$fieldName}"))
            ->helperText(__("schedules.{$fieldName}_helper"))
            ->multiple()
            ->searchable()
            ->options(function (): array {
                $servers = GetOoklaSpeedtestServers::run();

                return isset($servers['error']) ? [] : $servers;
            })
            ->getSearchResultsUsing(function (string $search): array {
                $servers = GetOoklaSpeedtestServers::run($search);

                return isset($servers['error']) ? [] : $servers;
            })
            ->getOptionLabelsUsing(function (array $values, ?Speedtest $record): array {
                $labels = $record?->server_labels ?? [];

                return collect($values)
                    ->mapWithKeys(fn ($value) => [$value => $labels[$value] ?? (string) $value])
                    ->toArray();
            })
            ->visible(fn (Get $get): bool => $get('server_mode') === $mode)
            ->dehydratedWhenHidden()
            ->dehydrateStateUsing(fn (?array $state): ?array => blank($state) ? null : array_values(array_map('strval', $state)));
    }
}
