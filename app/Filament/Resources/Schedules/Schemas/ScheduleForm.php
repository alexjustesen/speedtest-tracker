<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Actions\ExplainCronExpression;
use App\Rules\Cron;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;

class ScheduleForm
{
    public static function schema(): array
    {
        return [
            Section::make(__('schedules.details'))
                ->schema([
                    Toggle::make('is_active')
                        ->label(__('schedules.active'))
                        ->required(),
                    TextInput::make('name')
                        ->label(__('schedules.name'))
                        ->placeholder(__('schedules.name_placeholder'))
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->required(),
                    TextInput::make('description')
                        ->label(__('schedules.description'))
                        ->maxLength(255),
                    Select::make('type')
                        ->label(__('schedules.type'))
                        ->hidden(true)
                        ->options([
                            'Ookla' => 'Ookla',
                        ])
                        ->default('ookla')
                        ->native(false)
                        ->required(),
                ])
                ->columnSpan('full'),

            Tabs::make(__('schedules.options'))
                ->tabs([
                    Tab::make(__('schedules.schedule'))
                        ->schema([
                            TextInput::make('schedule')
                                ->label(__('schedules.schedule'))
                                ->placeholder(__('schedules.schedule_placeholder'))
                                ->helperText(fn (Get $get) => ExplainCronExpression::run($get('schedule')))
                                ->required()
                                ->rules([new Cron])
                                ->live(),
                            Placeholder::make('next_run_at')
                                ->label(__('schedules.next_run_at'))
                                ->content(function (Get $get) {
                                    $expression = $get('schedule');

                                    if (! $expression) {
                                        return '—';
                                    }

                                    try {
                                        $cron = new CronExpression($expression);

                                        return Carbon::instance(
                                            $cron->getNextRunDate(now(), 0, false, config('app.display_timezone'))
                                        )->toDayDateTimeString();
                                    } catch (\Exception $e) {
                                        return 'Invalid cron expression';
                                    }
                                }),
                        ]),

                    Tab::make(__('schedules.servers'))
                        ->schema([
                            Radio::make('options.server_preference')
                                ->label(__('schedules.server_preference'))
                                ->options([
                                    'auto' => __('schedules.server_preference_auto'),
                                    'prefer' => __('schedules.server_preference_prefer'),
                                    'ignore' => __('schedules.server_preference_ignore'),
                                ])
                                ->default('auto')
                                ->required()
                                ->live(),

                            TagsInput::make('options.servers')
                                ->label(__('schedules.server_id'))
                                ->placeholder(__('schedules.server_id_placeholder'))
                                ->hidden(fn (Get $get) => $get('options.server_preference') === 'auto'),
                        ]),

                    Tab::make(__('schedules.advanced'))
                        ->schema([
                            TagsInput::make('options.skip_ips')
                                ->label(__('schedules.skip_ips'))
                                ->placeholder(__('schedules.skip_ips_placeholder'))
                                ->nestedRecursiveRules([
                                    'ip',
                                ])
                                ->live()
                                ->helpertext(__('schedules.skip_ips_helper')),
                            TextInput::make('options.interface')
                                ->label(__('schedules.network_interface'))
                                ->placeholder(__('schedules.network_interface_placeholder'))
                                ->helpertext(__('schedules.network_interface_helper')),
                        ]),
                ])->columnSpan('full'),
        ];
    }
}
