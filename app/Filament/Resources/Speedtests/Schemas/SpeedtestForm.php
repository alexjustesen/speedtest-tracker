<?php

namespace App\Filament\Resources\Speedtests\Schemas;

use App\Actions\GetOoklaSpeedtestServers;
use App\Actions\Schedules\ExplainCronExpression;
use App\Enums\ServerMode;
use App\Models\Speedtest;
use App\Rules\Cron;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

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
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Toggle $component, ?Speedtest $record): void {
                                        if ($record?->schedule) {
                                            $component->state($record->schedule->isEnabled());
                                        }
                                    })
                                    ->columnSpanFull(),

                                TextInput::make('name')
                                    ->label(__('schedules.name'))
                                    ->placeholder(__('schedules.name_placeholder'))
                                    ->required()
                                    ->maxLength(255),

                                self::scheduleGroup(),
                            ]),

                        Tab::make(__('schedules.tab_servers'))
                            ->icon('tabler-server')
                            ->schema([
                                Radio::make('server_mode')
                                    ->label(__('schedules.server_mode'))
                                    ->options(ServerMode::class)
                                    ->default(ServerMode::Auto)
                                    ->required()
                                    ->live(),

                                self::serverSelect(),
                            ]),
                    ]),
            ]);
    }

    /**
     * The cron expression, saved directly to the related Cadence schedule together with the enabled toggle.
     */
    private static function scheduleGroup(): Group
    {
        $toScheduleAttributes = fn (array $data, Get $get): array => Speedtest::cronScheduleAttributes(
            expression: $data['expression'],
            enabled: (bool) $get('enabled'),
        );

        return Group::make()
            ->relationship('schedule')
            ->mutateRelationshipDataBeforeFillUsing(fn (array $data): array => [
                'expression' => $data['expression'] ?? null,
            ])
            ->mutateRelationshipDataBeforeCreateUsing($toScheduleAttributes)
            ->mutateRelationshipDataBeforeSaveUsing($toScheduleAttributes)
            ->schema([
                TextInput::make('expression')
                    ->label(__('schedules.schedule'))
                    ->placeholder(__('schedules.schedule_placeholder'))
                    ->hintAction(
                        Action::make('crontab_guru')
                            ->label('crontab.guru')
                            ->icon('tabler-external-link')
                            ->url('https://crontab.guru', shouldOpenInNewTab: true)
                    )
                    ->helperText(fn (Get $get) => ExplainCronExpression::run($get('expression')))
                    ->live(debounce: 500)
                    ->rules([new Cron])
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule
                            ->where('type', 'cron')
                            ->where('schedulable_type', (new Speedtest)->getMorphClass()),
                    )
                    ->validationMessages(['unique' => __('schedules.schedule_overlap')])
                    ->required()
                    ->maxLength(255),
            ]);
    }

    /**
     * Preferred servers, shown when the server mode is not automatic.
     */
    private static function serverSelect(): Select
    {
        $mode = fn (Get $get): ServerMode => self::serverMode($get('server_mode'));

        return Select::make('servers')
            ->label(__('schedules.servers'))
            ->helperText(__('schedules.servers_helper'))
            ->multiple()
            ->searchable()
            ->options(fn (): array => GetOoklaSpeedtestServers::options())
            ->getSearchResultsUsing(fn (string $search): array => GetOoklaSpeedtestServers::options($search))
            ->getOptionLabelsUsing(fn (array $values, ?Speedtest $record): array => collect($values)
                ->mapWithKeys(fn ($value) => [$value => $record?->server_labels[$value] ?? (string) $value])
                ->all())
            ->visible(fn (Get $get): bool => $mode($get) !== ServerMode::Auto)
            ->required(fn (Get $get): bool => $mode($get) !== ServerMode::Auto)
            ->dehydratedWhenHidden()
            ->dehydrateStateUsing(fn (?array $state, Get $get): ?array => $mode($get) === ServerMode::Auto || blank($state)
                ? null
                : array_values(array_map('strval', $state)));
    }

    /**
     * Normalize the server mode form state, which may be an enum instance or its raw value.
     */
    private static function serverMode(ServerMode|string|null $state): ServerMode
    {
        return $state instanceof ServerMode ? $state : (ServerMode::tryFrom((string) $state) ?? ServerMode::Auto);
    }
}
