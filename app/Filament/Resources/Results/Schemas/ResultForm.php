<?php

namespace App\Filament\Resources\Results\Schemas;

use App\Helpers\Number;
use App\Models\Result;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ResultForm
{
    public static function schema(): array
    {
        return ([
            Grid::make(['default' => 2, 'md' => 2])->schema([
                Grid::make()->schema([
                    Section::make('Result Overview')->schema([
                        TextInput::make('id')
                            ->label('ID'),
                        TextInput::make('created_at')
                            ->label('Created')
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                $component->state(Carbon::parse($state)
                                    ->timezone(config('app.display_timezone'))
                                    ->format(config('app.datetime_format')));
                            }),
                        TextInput::make('download')
                            ->label('Download')
                            ->afterStateHydrated(fn ($component, Result $record) => $component->state(! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : '')),
                        TextInput::make('upload')
                            ->label('Upload')
                            ->afterStateHydrated(fn ($component, Result $record) => $component->state(! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : '')),
                        TextInput::make('ping')
                            ->label('Ping')
                            ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                        TextInput::make('data.packetLoss')
                            ->label('Packet Loss')
                            ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', '').' %'),
                    ])->columns(2),

                    Section::make('Download Latency')
                        ->schema([
                            TextInput::make('data.download.latency.jitter')->label('Jitter')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                            TextInput::make('data.download.latency.high')->label('High')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                            TextInput::make('data.download.latency.low')->label('Low')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                            TextInput::make('data.download.latency.iqm')->label('IQM')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                        ])
                        ->columns(2)
                        ->collapsed(),

                    Section::make('Upload Latency')
                        ->schema([
                            TextInput::make('data.upload.latency.jitter')->label('Jitter')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                            TextInput::make('data.upload.latency.high')->label('High')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                            TextInput::make('data.upload.latency.low')->label('Low')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                            TextInput::make('data.upload.latency.iqm')->label('IQM')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                        ])
                        ->columns(2)
                        ->collapsed(),

                    Section::make('Ping Details')
                        ->schema([
                            TextInput::make('data.ping.jitter')->label('Jitter')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                            TextInput::make('data.ping.low')->label('Low')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                            TextInput::make('data.ping.high')->label('High')
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' ms'),
                        ])
                        ->columns(2)
                        ->collapsed(),

                    Textarea::make('data.message')
                        ->label('Message')
                        ->hint(new HtmlString('&#x1f517;<a href="https://docs.speedtest-tracker.dev/help/error-messages" target="_blank" rel="nofollow">Error Messages</a>'))
                        ->columnSpanFull(),
                ])->columnSpan([
                    'default' => 2,
                    'md' => 2,
                ]),

                Section::make('Server & Metadata')->schema([
                    TextEntry::make('service')
                        ->state(fn (Result $result): string => $result->service->getLabel()),
                    TextEntry::make('server_name')
                        ->state(fn (Result $result): ?string => $result->server_name),
                    TextEntry::make('server_id')
                        ->label('Server ID')
                        ->state(fn (Result $result): ?string => $result->server_id),
                    TextEntry::make('isp')
                        ->label('ISP')
                        ->state(fn (Result $result): ?string => $result->isp),
                    TextEntry::make('server_location')
                        ->label('Server Location')
                        ->state(fn (Result $result): ?string => $result->server_location),
                    TextEntry::make('server_host')
                        ->state(fn (Result $result): ?string => $result->server_host),
                    TextEntry::make('comment')
                        ->state(fn (Result $result): ?string => $result->comments),
                    Checkbox::make('scheduled'),
                    Checkbox::make('healthy'),
                ])->columns(1)->columnSpan([
                    'default' => 2,
                    'md' => 1,
                ]),
            ]),
        ]);
    }
} 