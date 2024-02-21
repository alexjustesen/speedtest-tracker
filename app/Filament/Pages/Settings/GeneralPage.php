<?php

namespace App\Filament\Pages\Settings;

use App\Actions\GetOoklaSpeedtestServers;
use App\Helpers\TimeZoneHelper;
use App\Rules\Cron;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class GeneralPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'General';

    protected static ?string $navigationLabel = 'General';

    protected static string $settings = GeneralSettings::class;

    public function mount(): void
    {
        parent::mount();

        abort_unless(auth()->user()->is_admin, 403);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->is_admin;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                ])
                    ->schema([
                        Forms\Components\Section::make('Site Settings')
                            ->schema([
                                Forms\Components\TextInput::make('site_name')
                                    ->maxLength(50)
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('public_dashboard_enabled')
                                    ->label('Public dashboard'),
                            ])
                            ->compact()
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        Forms\Components\Section::make('Time Zone Settings')
                            ->schema([
                                Forms\Components\Select::make('timezone')
                                    ->label('Time zone')
                                    ->hint(new HtmlString('&#x1f517;<a href="https://docs.speedtest-tracker.dev/" target="_blank" rel="nofollow">Docs</a>'))
                                    ->options(TimeZoneHelper::list())
                                    ->searchable()
                                    ->required(),
                                Forms\Components\TextInput::make('time_format')
                                    ->hint(new HtmlString('&#x1f517;<a href="https://www.php.net/manual/en/datetime.format.php" target="_blank" rel="nofollow">DateTime Format</a>'))
                                    ->placeholder('M j, Y G:i:s')
                                    ->maxLength(25)
                                    ->required(),
                                Forms\Components\Toggle::make('db_has_timezone')
                                    ->label('Database has time zone')
                                    ->helperText(new HtmlString('Enable if your database <strong>has</strong> a time zone already set.')),
                            ])
                            ->compact()
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        Forms\Components\Section::make('Speedtest Settings')
                            ->schema([
                                Forms\Components\TextInput::make('speedtest_schedule')
                                    ->rules([new Cron()])
                                    ->helperText('Leave empty to disable scheduled tests.')
                                    ->hint(new HtmlString('&#x1f517;<a href="https://crontab.cronhub.io/" target="_blank" rel="nofollow">Cron Generator</a>'))
                                    ->nullable()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('prune_results_older_than')
                                    ->helperText('Set to zero to disable pruning.')
                                    ->suffix('days')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(9999)
                                    ->columnSpan(1),
                                Forms\Components\Select::make('speedtest_server')
                                    ->label('Speedtest servers')
                                    ->helperText('Leave empty to let the system pick the best server.')
                                    ->maxItems(10)
                                    ->multiple()
                                    ->nullable()
                                    ->searchable()
                                    ->options(GetOoklaSpeedtestServers::run())
                                    ->getSearchResultsUsing(fn (string $search): array => $this->getServerSearchOptions($search))
                                    ->getOptionLabelsUsing(fn (array $values): array => $this->getServerLabels($values))
                                    ->columnSpanFull(),
                            ])
                            ->compact()
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    protected function getServerLabels(array $values): array
    {
        if (count($values) && is_null($values[0])) {
            return [];
        }

        return collect($values)->mapWithKeys(function (string $item, int $key) {
            return [$item => $item];
        })->toArray();
    }

    protected function getServerSearchOptions(string $search): array
    {
        $response = Http::get(
            url: 'https://www.speedtest.net/api/js/servers',
            query: [
                'engine' => 'js',
                'search' => $search,
                'https_functional' => true,
                'limit' => 20,
            ]
        );

        if ($response->failed()) {
            return [
                '' => 'There was an error retrieving Speedtest servers',
            ];
        }

        if (! $response->collect()->count() && is_numeric($search)) {
            return collect([
                [
                    'id' => $search,
                    'name' => $search.' (Manually entered server)',
                ],
            ])->pluck('name', 'id')->toArray();
        }

        return $response->collect()->mapWithKeys(function (array $item, int $key) {
            return [$item['id'] => $item['id'].': '.$item['name'].' ('.$item['sponsor'].')'];
        })->toArray();
    }
}
