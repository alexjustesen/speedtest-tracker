<?php

namespace App\Filament\Resources;

use App\Enums\ResultStatus;
use App\Filament\Exports\ResultExporter;
use App\Filament\Resources\ResultResource\Pages;
use App\Helpers\Number;
use App\Helpers\Time;
use App\Jobs\TruncateResults;
use App\Models\Result;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number as LaravelNumber;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(['default' => 2, 'md' => 3])->schema([
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

                    Section::make('Other')
                        ->schema([
                            Fieldset::make('Test Duration')->schema([
                                TextInput::make('download_elapsed')->label('Download')
                                    ->afterStateHydrated(fn ($component, Result $record) => $component->state(Time::formatElapsed($record->downloadElapsed ?? 0))),
                                TextInput::make('upload_elapsed')->label('Upload')
                                    ->afterStateHydrated(fn ($component, Result $record) => $component->state(Time::formatElapsed($record->uploadElapsed ?? 0))),
                            ])->columns(2),
                            Fieldset::make('Transferred Data')->schema([
                                TextInput::make('downloaded.bytes')->label('Download')
                                    ->afterStateHydrated(function (TextInput $component, Result $record) {
                                        $component->state(! blank($record->downloaded_bytes) ? LaravelNumber::fileSize(bytes: $record->downloaded_bytes, precision: 2) : '');
                                    }),
                                TextInput::make('uploaded.bytes')->label('Upload')
                                    ->afterStateHydrated(function (TextInput $component, Result $record) {
                                        $component->state(! blank($record->uploaded_bytes) ? LaravelNumber::fileSize(bytes: $record->uploaded_bytes, precision: 2) : '');
                                    }),
                            ])->columns(2),
                        ])
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
                    Placeholder::make('service')
                        ->content(fn (Result $result): string => $result->service->getLabel()),
                    Placeholder::make('server_name')
                        ->content(fn (Result $result): ?string => $result->server_name),
                    Placeholder::make('server_id')
                        ->label('Server ID')
                        ->content(fn (Result $result): ?string => $result->server_id),
                    Placeholder::make('isp')
                        ->label('ISP')
                        ->content(fn (Result $result): ?string => $result->isp),
                    Placeholder::make('server_location')
                        ->label('Server Location')
                        ->content(fn (Result $result): ?string => $result->server_location),
                    Placeholder::make('server_host')
                        ->content(fn (Result $result): ?string => $result->server_host),
                    Placeholder::make('comment')
                        ->content(fn (Result $result): ?string => $result->comments),
                    Checkbox::make('scheduled'),
                    Checkbox::make('healthy'),
                ])->columns(1)->columnSpan([
                    'default' => 2,
                    'md' => 1,
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
                TextColumn::make('data.interface.externalIp')
                    ->label('IP address')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->interface->externalIp', $direction);
                    }),
                TextColumn::make('service')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                TextColumn::make('data.server.id')
                    ->label('Server ID')
                    ->toggleable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->id', $direction);
                    }),
                TextColumn::make('data.isp')
                    ->label('ISP')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->isp', $direction);
                    }),
                TextColumn::make('data.server.location')
                    ->label('Server Location')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->location', $direction);
                    }),
                TextColumn::make('data.server.name')
                    ->toggleable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->name', $direction);
                    }),
                TextColumn::make('download')
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : null)
                    ->sortable(),
                TextColumn::make('upload')
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : null)
                    ->sortable(),
                TextColumn::make('ping')
                    ->toggleable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.download.latency.jitter')
                    ->label('Download jitter')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.download.latency.high')
                    ->label('Download latency high')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.download.latency.low')
                    ->label('Download latency low')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.download.latency.iqm')
                    ->label('Download latency iqm')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->iqm', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.upload.latency.jitter')
                    ->label('Upload jitter')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.upload.latency.high')
                    ->label('Upload latency high')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.upload.latency.low')
                    ->label('Upload latency low')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.upload.latency.iqm')
                    ->label('Upload latency iqm')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->iqm', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.ping.jitter')
                    ->label('Ping jitter')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->ping->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.ping.low')
                    ->label('Ping low')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->ping->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.ping.high')
                    ->label('Ping high')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->ping->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('packet_loss')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->packetLoss', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 2, '.', '').' %';
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->toggleable()
                    ->sortable(),
                IconColumn::make('scheduled')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->alignment(Alignment::Center),
                IconColumn::make('healthy')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignment(Alignment::Center),
                TextColumn::make('data.message')
                    ->label('Error Message')
                    ->limit(15)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->sortable()
                    ->alignment(Alignment::End),
                TextColumn::make('updated_at')
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignment(Alignment::End),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('ip_address')
                    ->label('IP address')
                    ->multiple()
                    ->options(function (): array {
                        return Result::query()
                            ->select('data->interface->externalIp AS public_ip_address')
                            ->whereNotNull('data->interface->externalIp')
                            ->where('status', '=', ResultStatus::Completed)
                            ->distinct()
                            ->orderBy('data->interface->externalIp')
                            ->get()
                            ->mapWithKeys(function (Result $item, int $key) {
                                return [$item['public_ip_address'] => $item['public_ip_address']];
                            })
                            ->toArray();
                    })
                    ->attribute('data->interface->externalIp'),
                SelectFilter::make('server_name')
                    ->label('Server name')
                    ->multiple()
                    ->options(function (): array {
                        return Result::query()
                            ->select('data->server->name AS data_server_name')
                            ->whereNotNull('data->server->name')
                            ->where('status', '=', ResultStatus::Completed)
                            ->distinct()
                            ->orderBy('data->server->name')
                            ->get()
                            ->mapWithKeys(function (Result $item, int $key) {
                                return [$item['data_server_name'] => $item['data_server_name']];
                            })
                            ->toArray();
                    })
                    ->attribute('data->server->name'),
                TernaryFilter::make('scheduled')
                    ->nullable()
                    ->trueLabel('Only scheduled speedtests')
                    ->falseLabel('Only manual speedtests')
                    ->queries(
                        true: fn (Builder $query) => $query->where('scheduled', true),
                        false: fn (Builder $query) => $query->where('scheduled', false),
                        blank: fn (Builder $query) => $query,
                    ),
                SelectFilter::make('status')
                    ->multiple()
                    ->options(ResultStatus::class),
                TernaryFilter::make('healthy')
                    ->nullable()
                    ->trueLabel('Only healthy speedtests')
                    ->falseLabel('Only unhealthy speedtests')
                    ->queries(
                        true: fn (Builder $query) => $query->where('healthy', true),
                        false: fn (Builder $query) => $query->where('healthy', false),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('view result')
                        ->label('View on Speedtest.net')
                        ->icon('heroicon-o-link')
                        ->url(fn (Result $record): ?string => $record->result_url)
                        ->hidden(fn (Result $record): bool => $record->status !== ResultStatus::Completed)
                        ->openUrlInNewTab(),
                    ViewAction::make(),
                    Action::make('updateComments')
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->hidden(fn (): bool => ! (Auth::user()?->is_admin ?? false) && ! (Auth::user()?->is_user ?? false))
                        ->mountUsing(fn (Forms\ComponentContainer $form, Result $record) => $form->fill([
                            'comments' => $record->comments,
                        ]))
                        ->action(function (Result $record, array $data): void {
                            $record->comments = $data['comments'];
                            $record->save();
                        })
                        ->form([
                            Textarea::make('comments')
                                ->rows(6)
                                ->maxLength(500),
                        ]),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ResultExporter::class)
                    ->columnMapping(false)
                    ->requiresConfirmation()
                    ->modalHeading('Export all results?')
                    ->modalDescription('This will export all columns for all results.')
                    ->fileName(fn (): string => 'results-'.now()->timestamp),
                ActionGroup::make([
                    Action::make('truncate')
                        ->action(fn () => TruncateResults::dispatch(Auth::user()))
                        ->requiresConfirmation()
                        ->modalHeading('Truncate Results')
                        ->modalDescription('Are you sure you want to truncate all results data? This can\'t be undone.')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->hidden(fn (): bool => ! Auth::user()->is_admin),
                ])->dropdownPlacement('bottom-end'),
            ])
            ->defaultSort('id', 'desc')
            ->deferLoading()
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResults::route('/'),
        ];
    }
}
