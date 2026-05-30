<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Actions\ExplainCronExpression;
use App\Actions\GetOoklaSpeedtestServers;
use App\Rules\Cron;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ScheduleForm
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
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('schedules.name'))
                                    ->placeholder(__('schedules.name_placeholder'))
                                    ->required()
                                    ->maxLength(255),

                                Toggle::make('enabled')
                                    ->label(__('schedules.enabled'))
                                    ->default(true),

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
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages(['unique' => __('schedules.schedule_overlap')])
                                    ->rules([new Cron])
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Tab::make(__('schedules.tab_servers'))
                            ->icon('tabler-server')
                            ->schema([
                                Select::make('server_mode')
                                    ->label(__('schedules.server_mode'))
                                    ->options(__('schedules.server_mode_options'))
                                    ->default('auto')
                                    ->native(false)
                                    ->live()
                                    ->saved(false)
                                    ->afterStateHydrated(function (Select $component, Get $get) {
                                        if (! blank($get('servers'))) {
                                            $component->state('prefer');
                                        } elseif (! blank($get('blocked_servers'))) {
                                            $component->state('block');
                                        } else {
                                            $component->state('auto');
                                        }
                                    })
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('servers', null);
                                        $set('blocked_servers', null);
                                    }),

                                Select::make('servers')
                                    ->label(__('schedules.servers'))
                                    ->helperText(__('schedules.servers_helper'))
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
                                    ->getOptionLabelsUsing(function (array $values): array {
                                        $servers = GetOoklaSpeedtestServers::run();
                                        $available = isset($servers['error']) ? [] : $servers;

                                        return collect($values)
                                            ->mapWithKeys(fn ($value) => [$value => $available[$value] ?? (string) $value])
                                            ->toArray();
                                    })
                                    ->createOptionForm([
                                        TextInput::make('server_id')
                                            ->label(__('schedules.server_id_manual'))
                                            ->required()
                                            ->integer(),
                                    ])
                                    ->createOptionUsing(fn (array $data): string => (string) $data['server_id'])
                                    ->visible(fn (Get $get): bool => $get('server_mode') === 'prefer')
                                    ->dehydratedWhenHidden()
                                    ->dehydrateStateUsing(fn (?array $state): ?array => blank($state) ? null : array_values(array_map('strval', $state))
                                    ),

                                Select::make('blocked_servers')
                                    ->label(__('schedules.blocked_servers'))
                                    ->helperText(__('schedules.blocked_servers_helper'))
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
                                    ->getOptionLabelsUsing(function (array $values): array {
                                        $servers = GetOoklaSpeedtestServers::run();
                                        $available = isset($servers['error']) ? [] : $servers;

                                        return collect($values)
                                            ->mapWithKeys(fn ($value) => [$value => $available[$value] ?? (string) $value])
                                            ->toArray();
                                    })
                                    ->createOptionForm([
                                        TextInput::make('server_id')
                                            ->label(__('schedules.server_id_manual'))
                                            ->required()
                                            ->integer(),
                                    ])
                                    ->createOptionUsing(fn (array $data): string => (string) $data['server_id'])
                                    ->visible(fn (Get $get): bool => $get('server_mode') === 'block')
                                    ->dehydratedWhenHidden()
                                    ->dehydrateStateUsing(fn (?array $state): ?array => blank($state) ? null : array_values(array_map('strval', $state))
                                    ),
                            ]),

                        Tab::make(__('schedules.tab_network'))
                            ->icon('tabler-network')
                            ->schema([
                                TextInput::make('interface')
                                    ->label(__('schedules.interface'))
                                    ->helperText(__('schedules.interface_helper'))
                                    ->placeholder(__('schedules.interface_placeholder'))
                                    ->maxLength(255)
                                    ->nullable(),

                                TextInput::make('skip_ips')
                                    ->label(__('schedules.skip_ips'))
                                    ->helperText(__('schedules.skip_ips_helper'))
                                    ->placeholder('1.2.3.4,10.0.0.0/8')
                                    ->formatStateUsing(fn (?array $state): string => implode(',', $state ?? []))
                                    ->dehydrateStateUsing(fn (?string $state): ?array => self::parseCommaSeparated($state))
                                    ->nullable(),
                            ]),
                    ]),
            ]);
    }

    /**
     * @return array<string>|null
     */
    private static function parseCommaSeparated(?string $value): ?array
    {
        if (blank($value)) {
            return null;
        }

        $items = array_values(array_filter(array_map('trim', explode(',', $value))));

        return empty($items) ? null : $items;
    }
}
