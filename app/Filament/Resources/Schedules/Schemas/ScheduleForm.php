<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Actions\ExplainCronExpression;
use App\Actions\GetOoklaSpeedtestServers;
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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ScheduleForm
{
    public static function schema(): array
    {
        return [
            Grid::make([
                'default' => 1,
                'lg' => 2,
            ])->schema([
                Section::make('Details')
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('Enter a name for the test.')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->required(),
                        TextInput::make('description')
                            ->maxLength(255),
                    ])
                    ->columnSpan([
                        'default' => 1,
                    ]),

                Section::make('Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->required(),
                        Select::make('owned_by_id')
                            ->label('Owner')
                            ->placeholder('Select an owner.')
                            ->relationship('ownedBy', 'name')
                            ->default(Auth::id())
                            ->searchable(),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'Ookla' => 'Ookla',
                            ])
                            ->default('Ookla')
                            ->native(false)
                            ->required(),
                        TextInput::make('token')
                            ->helperText(new HtmlString('This is a secret token that can be used to authenticate requests to the test.'))
                            ->readOnly()
                            ->hiddenOn('create'),
                    ])
                    ->columnSpan([
                        'default' => 1,
                    ]),

                Tabs::make('Options')
                    ->tabs([
                        Tab::make('Schedule')
                            ->schema([
                                TextInput::make('options.cron_expression')
                                    ->placeholder('Enter a cron expression.')
                                    ->helperText(fn (Get $get) => ExplainCronExpression::run($get('options.cron_expression')))
                                    ->required()
                                    ->rules([new Cron])
                                    ->live(),
                                Placeholder::make('next_run_at')
                                    ->label('Next Run At')
                                    ->content(function (Get $get) {
                                        $expression = $get('options.cron_expression');

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

                        Tab::make('Servers')
                            ->schema([
                                Radio::make('options.server_preference')
                                    ->options([
                                        'auto' => 'Automatically select a server',
                                        'prefer' => 'Prefer servers from the list',
                                        'ignore' => 'Ignore servers from the list',
                                    ])
                                    ->default('auto')
                                    ->required()
                                    ->live(),

                                Repeater::make('options.servers')
                                    ->schema([
                                        Select::make('server_id')
                                            ->label('Server ID')
                                            ->placeholder('Select the ID of the server.')
                                            ->options(function (): array {
                                                return GetOoklaSpeedtestServers::run();
                                            })
                                            ->searchable()
                                            ->required(),
                                    ])
                                    ->minItems(1)
                                    ->maxItems(20)
                                    ->hidden(fn (Get $get) => $get('options.server_preference') === 'auto'),
                            ]),

                        Tab::make('Advanced')
                            ->schema([
                                TagsInput::make('options.skip_ips')
                                    ->label('Skip IP addresses')
                                    ->placeholder('8.8.8.8')
                                    ->nestedRecursiveRules([
                                        'ip',
                                    ])
                                    ->live()
                                    ->helpertext('Add external IP addresses that should be skipped.'),
                                TextInput::make('options.interface')
                                    ->label('Network Interface')
                                    ->placeholder('eth0')
                                    ->helpertext('Set the network interface to use for the test. This need to be the network interface available inside the container'),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]),
        ];
    }
}
