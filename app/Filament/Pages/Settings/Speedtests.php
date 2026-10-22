<?php

namespace App\Filament\Pages\Settings;

use App\Enums\ResultService;
use App\Rules\Cron;
use App\Rules\Iperf3Server;
use App\Services\ScheduledSpeedtestService;
use App\Settings\SpeedtestSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Speedtests extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-rocket';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        return __('settings/speedtests.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings/speedtests.label');
    }

    protected static string $settings = SpeedtestSettings::class;

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(['default' => 1])
                            ->schema([
                                Section::make(__('settings/speedtests.schedules'))
                                    ->description(fn (): string => ScheduledSpeedtestService::environmentSchedule()
                                        ? __('settings/speedtests.schedules_description_env', ['cron' => ScheduledSpeedtestService::environmentSchedule()])
                                        : __('settings/speedtests.schedules_description'))
                                    ->schema([
                                        Repeater::make('schedules')
                                            ->hiddenLabel()
                                            ->schema([
                                                Select::make('service')
                                                    ->label(__('results.service'))
                                                    ->options(ResultService::selectable())
                                                    ->default(ResultService::Ookla->value)
                                                    ->selectablePlaceholder(false)
                                                    ->required()
                                                    ->live(),
                                                TextInput::make('cron')
                                                    ->label(__('settings/speedtests.cron'))
                                                    ->helperText(__('settings/speedtests.cron_helper'))
                                                    ->placeholder('0 * * * *')
                                                    ->rules([new Cron])
                                                    ->required(),
                                                TextInput::make('target')
                                                    ->label(fn (Get $get): string => self::targetLabel($get('service')))
                                                    ->helperText(fn (Get $get): string => self::targetHelper($get('service')))
                                                    ->datalist(fn (Get $get): array => self::targetSuggestions($get('service'), $get('../../iperf3_servers'), $get('../../file_urls')))
                                                    ->rules(fn (Get $get): array => self::targetRules($get('service')))
                                                    ->visible(fn (Get $get): bool => ! in_array($get('service'), [ResultService::Cloudflare->value, ResultService::Yandex->value], true)),
                                                Toggle::make('enabled')
                                                    ->label(__('settings/speedtests.enabled'))
                                                    ->default(true)
                                                    ->inline(false),
                                            ])
                                            ->columns(['default' => 1, 'md' => 4])
                                            ->itemLabel(fn (array $state): string => self::scheduleLabel($state))
                                            ->addActionLabel(__('settings/speedtests.add_schedule'))
                                            ->collapsible()
                                            ->reorderable(false)
                                            ->defaultItems(0),
                                    ])
                                    ->compact()
                                    ->columnSpan('full'),

                                Section::make(__('settings/speedtests.sources'))
                                    ->description(__('settings/speedtests.sources_description'))
                                    ->schema([
                                        Repeater::make('iperf3_servers')
                                            ->label(__('settings/speedtests.iperf3_servers'))
                                            ->simple(
                                                TextInput::make('server')
                                                    ->placeholder('iperf.example.com:5201')
                                                    ->rules(self::targetRules(ResultService::Iperf3->value))
                                                    ->required(),
                                            )
                                            ->addActionLabel(__('settings/speedtests.add_iperf3_server'))
                                            ->defaultItems(0)
                                            ->live(),
                                        Repeater::make('file_urls')
                                            ->label(__('settings/speedtests.file_urls'))
                                            ->simple(
                                                TextInput::make('url')
                                                    ->placeholder('https://example.com/100MB.bin')
                                                    ->rules(self::targetRules(ResultService::File->value))
                                                    ->required(),
                                            )
                                            ->addActionLabel(__('settings/speedtests.add_file_url'))
                                            ->defaultItems(0)
                                            ->live(),
                                    ])
                                    ->compact()
                                    ->columnSpan('full'),
                            ])
                            ->columnSpan(['md' => 2]),

                        Grid::make(['default' => 1])
                            ->schema([
                                Section::make(__('settings/speedtests.dialog'))
                                    ->schema([
                                        Select::make('default_service')
                                            ->label(__('settings/speedtests.default_service'))
                                            ->helperText(__('settings/speedtests.default_service_helper'))
                                            ->options(ResultService::selectable())
                                            ->selectablePlaceholder(false)
                                            ->required(),
                                    ])
                                    ->compact(),

                                Section::make(__('settings/speedtests.measurement'))
                                    ->description(__('settings/speedtests.measurement_description'))
                                    ->schema([
                                        TextInput::make('http_duration')
                                            ->label(__('settings/speedtests.http_duration'))
                                            ->hint(__('settings/speedtests.seconds'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(30)
                                            ->required(),
                                        TextInput::make('http_streams')
                                            ->label(__('settings/speedtests.http_streams'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(16)
                                            ->required(),
                                        TextInput::make('iperf3_duration')
                                            ->label(__('settings/speedtests.iperf3_duration'))
                                            ->hint(__('settings/speedtests.seconds'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(30)
                                            ->required(),
                                        TextInput::make('iperf3_streams')
                                            ->label(__('settings/speedtests.iperf3_streams'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(16)
                                            ->required(),
                                    ])
                                    ->compact(),
                            ])
                            ->columnSpan(['md' => 1]),
                    ]),
            ]);
    }

    /**
     * Cast the numeric fields, the form keeps them as strings.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach (['http_duration', 'http_streams', 'iperf3_duration', 'iperf3_streams'] as $key) {
            $data[$key] = (int) $data[$key];
        }

        $data['schedules'] = collect($data['schedules'] ?? [])
            ->map(fn (array $schedule): array => [
                'enabled' => (bool) ($schedule['enabled'] ?? true),
                'service' => $schedule['service'],
                'cron' => trim($schedule['cron']),
                'target' => filled($schedule['target'] ?? null) ? trim($schedule['target']) : null,
            ])
            ->values()
            ->toArray();

        $data['iperf3_servers'] = array_values(array_map('trim', $data['iperf3_servers'] ?? []));
        $data['file_urls'] = array_values(array_map('trim', $data['file_urls'] ?? []));

        return $data;
    }

    protected static function scheduleLabel(array $state): string
    {
        $service = ResultService::tryFrom((string) ($state['service'] ?? ''))?->getLabel() ?? '';

        return collect([$service, $state['cron'] ?? null, $state['target'] ?? null])
            ->filter()
            ->implode(' · ')
            .(($state['enabled'] ?? true) ? '' : ' ('.__('settings/speedtests.disabled').')');
    }

    protected static function targetLabel(?string $service): string
    {
        return match (ResultService::tryFrom((string) $service)) {
            ResultService::Ookla => __('results.server_id'),
            ResultService::Iperf3 => __('results.iperf3_server'),
            ResultService::File => __('results.file_url'),
            default => __('settings/speedtests.target'),
        };
    }

    protected static function targetHelper(?string $service): string
    {
        return match (ResultService::tryFrom((string) $service)) {
            ResultService::Ookla => __('settings/speedtests.target_helper_ookla'),
            default => __('settings/speedtests.target_helper_source'),
        };
    }

    /**
     * Suggest the sources entered on this page, the simple repeaters keep each one as ['server' => ...] or ['url' => ...].
     */
    protected static function targetSuggestions(?string $service, mixed $iperf3Servers, mixed $fileUrls): array
    {
        $sources = match (ResultService::tryFrom((string) $service)) {
            ResultService::Iperf3 => $iperf3Servers,
            ResultService::File => $fileUrls,
            default => [],
        };

        return collect(Arr::wrap($sources))
            ->map(fn (mixed $source): mixed => is_array($source) ? Arr::first($source) : $source)
            ->filter(fn (mixed $source): bool => is_string($source) && filled($source))
            ->values()
            ->toArray();
    }

    protected static function targetRules(?string $service): array
    {
        return match (ResultService::tryFrom((string) $service)) {
            ResultService::Ookla => ['nullable', 'integer'],
            ResultService::File => ['nullable', 'url:http,https'],
            ResultService::Iperf3 => ['nullable', new Iperf3Server],
            default => [],
        };
    }
}
