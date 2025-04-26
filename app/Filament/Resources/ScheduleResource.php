<?php

namespace App\Filament\Resources;

use App\Actions\ExplainCronExpression;
use App\Actions\GetOoklaSpeedtestServers;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use App\Rules\Cron;
use App\Rules\NoCronOverlap;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])->schema([
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
                            ]),

                        Tabs::make('Options')
                            ->tabs([
                                Tab::make('Schedule')
                                    ->schema([
                                        TextInput::make('options.cron_expression')
                                            ->placeholder('Enter a cron expression.')
                                            ->helperText(fn (Get $get) => ExplainCronExpression::run($get('options.cron_expression')))
                                            ->required()
                                            ->rules([
                                                new Cron,
                                                fn (?Schedule $record, Get $get): NoCronOverlap => new NoCronOverlap(
                                                    new Schedule(['type' => $get('type') ?? $record?->type]),
                                                    $record?->id,
                                                    (bool) ($get('is_active') ?? $record?->is_active),
                                                ),
                                            ])
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
                                Tab::make('Thresholds')
                                    ->schema([
                                        Toggle::make('thresholds.enabled')
                                            ->label('Enable Threshold')
                                            ->helperText('Thresholds do not take into account previous history and could be triggered on each test')
                                            ->live()
                                            ->default(false),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('thresholds.download')
                                                    ->label('Download')
                                                    ->hint('Mbps')
                                                    ->helperText('Set to zero to disable this metric.')
                                                    ->default(0)
                                                    ->minValue(0)
                                                    ->numeric()
                                                    ->required()
                                                    ->visible(fn ($get) => $get('thresholds.enabled') === true),

                                                TextInput::make('thresholds.upload')
                                                    ->label('Upload')
                                                    ->hint('Mbps')
                                                    ->helperText('Set to zero to disable this metric.')
                                                    ->default(0)
                                                    ->minValue(0)
                                                    ->numeric()
                                                    ->required()
                                                    ->visible(fn ($get) => $get('thresholds.enabled') === true),

                                                TextInput::make('thresholds.ping')
                                                    ->label('Ping')
                                                    ->hint('ms')
                                                    ->helperText('Set to zero to disable this metric.')
                                                    ->default(0)
                                                    ->minValue(0)
                                                    ->numeric()
                                                    ->required()
                                                    ->visible(fn ($get) => $get('thresholds.enabled') === true),
                                            ]),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        // ...
                    ])->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                    Grid::make([
                        'default' => 1,
                    ])->schema([
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
                            ]),

                        // ...
                    ])->columnSpan([
                        'default' => 1,
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('token')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name'),
                TextColumn::make('type')
                    ->label('Type')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('description')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('options.cron_expression')
                    ->label('Schedule')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn (?string $state) => ExplainCronExpression::run($state)),
                TextColumn::make('options.server_preference')
                    ->label('Server Preference')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function (?string $state) {
                        return match ($state) {
                            'auto' => 'Automatic',
                            'prefer' => 'Prefer Specific Servers',
                            'ignore' => 'Ignore Specific Servers',
                        };
                    })
                    ->tooltip(fn ($record) => $record->getServerTooltip()),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->boolean(),
                IconColumn::make('thresholds.enabled')
                    ->label('Thresholds')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(fn ($record) => $record->getThresholdTooltip())
                    ->boolean(),
                TextColumn::make('ownedBy.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('next_run_at')
                    ->alignEnd()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(function () {
                        return Schedule::distinct()
                            ->pluck('type', 'type')
                            ->toArray();
                    })
                    ->native(false),
                TernaryFilter::make('Active')
                    ->nullable()
                    ->trueLabel('Active schedules only')
                    ->falseLabel('Inactive schedules only')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                        blank: fn (Builder $query) => $query,
                    )
                    ->native(false),
                SelectFilter::make('options.server_preference')
                    ->label('Server Preference')
                    ->options(function () {
                        return Schedule::distinct()
                            ->get()
                            ->pluck('options')
                            ->map(function ($options) {
                                return $options['server_preference'] ?? null;
                            })
                            ->filter()
                            ->unique()
                            ->mapWithKeys(function ($value) {
                                return [
                                    $value => match ($value) {
                                        'auto' => 'Automatic',
                                        'prefer' => 'Prefer Specific Servers',
                                        'ignore' => 'Ignore Specific Servers',
                                    },
                                ];
                            })
                            ->toArray();
                    })
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('viewResults')
                        ->label('View Results')
                        ->action(function ($record) {
                            return redirect()->route('filament.admin.resources.results.index', [
                                'tableFilters[schedule_id][values][0]' => $record->id,
                            ]);
                        })
                        ->icon('heroicon-s-eye'),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedule::route('/'),
        ];
    }
}
