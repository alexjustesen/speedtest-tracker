<?php

namespace App\Filament\Pages\Settings;

use App\Rules\Cron;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Http;
use Squire\Models\Timezone;

class GeneralPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'General';

    protected static ?string $navigationLabel = 'General';

    protected static string $settings = GeneralSettings::class;

    protected function getMaxContentWidth(): string
    {
        $settings = new GeneralSettings();

        return $settings->content_width;
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make([
                'default' => 1,
                'md' => 3,
            ])
                ->schema([
                    Grid::make([
                        'default' => 1,
                    ])
                        ->schema([
                            Section::make('Site Settings')
                                ->schema([
                                    TextInput::make('site_name')
                                        ->maxLength(50)
                                        ->required()
                                        ->columnSpan(['md' => 2]),
                                    Select::make('timezone')
                                        ->options(Timezone::all()->pluck('code', 'code'))
                                        ->searchable()
                                        ->required(),
                                    TextInput::make('time_format')
                                        ->helperText('Use [DateTime Format](https://www.php.net/manual/en/datetime.format.php) options to change the format of the datetime in views.')
                                        ->placeholder('M j, Y G:i:s')
                                        ->maxLength(25)
                                        ->required(),
                                    Select::make('content_width')
                                        ->options([
                                            '7xl' => 'Default width',
                                            'full' => 'Full width',
                                        ]),
                                ])
                                ->compact()
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),

                            Section::make('Speedtest Settings')
                                ->schema([
                                    TextInput::make('speedtest_schedule')
                                        ->rules([new Cron()])
                                        ->helperText('Leave empty to disable the schedule. You can also use the cron expression generator [HERE](https://crontab.cronhub.io/) to help you make schedules.')
                                        ->nullable()
                                        ->columnSpan(1),
                                    Select::make('speedtest_server')
                                        ->label('Speedtest server ID')
                                        ->helperText('Leave empty to let the system pick the best server.')
                                        ->nullable()
                                        ->multiple()
                                        ->maxItems(10)
                                        ->searchable()
                                        ->options(function () {
                                            $response = Http::get(
                                                url: 'https://www.speedtest.net/api/js/servers',
                                                query: [
                                                    'engine' => 'js',
                                                    'https_functional' => true,
                                                    'limit' => 10,
                                                ]
                                            );

                                            if ($response->failed()) {
                                                return [
                                                    '' => 'There was an error retrieving Speedtest servers',
                                                ];
                                            }

                                            return $response->collect()->map(function ($item) {
                                                return [
                                                    'id' => $item['id'],
                                                    'name' => $item['id'].': '.$item['name'].' ('.$item['sponsor'].')',
                                                ];
                                            })->pluck('name', 'id');
                                        })
                                        ->getSearchResultsUsing(function (string $search) {
                                            $response = Http::get(
                                                url: 'https://www.speedtest.net/api/js/servers',
                                                query: [
                                                    'engine' => 'js',
                                                    'search' => $search,
                                                    'https_functional' => true,
                                                    'limit' => 10,
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
                                                        'name' => $search.': No server found, manually add this ID.',
                                                    ],
                                                ]);
                                            }

                                            return $response->collect()->map(function ($item) {
                                                return [
                                                    'id' => $item['id'],
                                                    'name' => $item['id'].': '.$item['name'].' ('.$item['sponsor'].')',
                                                ];
                                            })->pluck('name', 'id');
                                        })
                                        ->columnSpan(2),
                                ])
                                ->compact()
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),
                        ])
                        ->columnSpan([
                            'md' => 2,
                        ]),
                ]),
        ];
    }
}
