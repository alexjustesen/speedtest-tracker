<?php

namespace App\Filament\Resources;

use App\Enums\ResultStatus;
use App\Filament\Exports\ResultExporter;
use App\Filament\Resources\ResultResource\Pages;
use App\Helpers\Number;
use App\Jobs\TruncateResults;
use App\Models\Result;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 2,
                    'md' => 3,
                ])->schema([
                    Forms\Components\Grid::make([
                        'default' => 2,
                        'md' => 3,
                    ])
                        ->schema([
                            Forms\Components\TextInput::make('id')
                                ->label('ID'),
                            Forms\Components\TextInput::make('created_at')
                                ->label('Created')
                                ->afterStateHydrated(function (TextInput $component, $state) {
                                    $component->state(Carbon::parse($state)->timezone(config('app.display_timezone'))->format(config('app.datetime_format')));
                                })
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('download')
                                ->label('Download')
                                ->afterStateHydrated(function (TextInput $component, Result $record) {
                                    $component->state(! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : '');
                                }),
                            Forms\Components\TextInput::make('upload')
                                ->label('Upload')
                                ->afterStateHydrated(function (TextInput $component, Result $record) {
                                    $component->state(! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : '');
                                }),
                            Forms\Components\TextInput::make('ping')
                                ->label('Ping')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.download.latency.jitter')
                                ->label('Download Jitter (ms)')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.download.latency.high')
                                ->label('Download Latency High')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.download.latency.low')
                                ->label('Download Latency low')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.download.latency.iqm')
                                ->label('Download Latency iqm')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.upload.latency.jitter')
                                ->label('Upload Jitter')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.upload.latency.high')
                                ->label('Upload Latency High')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.upload.latency.low')
                                ->label('Upload Latency low')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.upload.latency.iqm')
                                ->label('Upload Latency iqm')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.ping.jitter')
                                ->label('Ping Jitter')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 0, '.', '').' ms';
                                }),
                            Forms\Components\TextInput::make('data.packetLoss')
                                ->label('Packet Loss')
                                ->formatStateUsing(function ($state) {
                                    return number_format((float) $state, 2, '.', '').' %';
                                }),
                            Forms\Components\Textarea::make('data.message')
                                ->label('Message')
                                ->hint(new HtmlString('&#x1f517;<a href="https://docs.speedtest-tracker.dev/help/error-messages" target="_blank" rel="nofollow">Error Messages</a>'))
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(2),
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Placeholder::make('service')
                                ->content(fn (Result $result): string => $result->service->getLabel()),
                            Forms\Components\Placeholder::make('server_name')
                                ->content(fn (Result $result): ?string => $result->server_name),
                            Forms\Components\Placeholder::make('server_id')
                                ->label('Server ID')
                                ->content(fn (Result $result): ?string => $result->server_id),
                            Forms\Components\Placeholder::make('isp')
                                ->label('ISP')
                                ->content(fn (Result $result): ?string => $result->isp),
                            Forms\Components\Placeholder::make('server_location')
                                ->label('Server Location')
                                ->content(fn (Result $result): ?string => $result->server_location),
                            Forms\Components\Placeholder::make('server_host')
                                ->content(fn (Result $result): ?string => $result->server_host),
                            Forms\Components\Placeholder::make('comment')
                                ->content(fn (Result $result): ?string => $result->comments),
                            Forms\Components\Placeholder::make('schedule_id')
                                ->label('Test Schedule')
                                ->content(fn ($record) => $record->schedule->name ?? 'N/A'),
                            Forms\Components\Checkbox::make('scheduled'),
                            Forms\Components\Checkbox::make('retry')
                                ->label('Retry'),
                            Forms\Components\Checkbox::make('healthy'),
                        ])
                        ->columns(1)
                        ->columnSpan([
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data.interface.externalIp')
                    ->label('IP address')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->interface->externalIp', $direction);
                    }),
                Tables\Columns\TextColumn::make('service')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data.server.id')
                    ->label('Server ID')
                    ->toggleable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->id', $direction);
                    }),
                Tables\Columns\TextColumn::make('data.isp')
                    ->label('ISP')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->isp', $direction);
                    }),
                Tables\Columns\TextColumn::make('data.server.location')
                    ->label('Server Location')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->location', $direction);
                    }),
                Tables\Columns\TextColumn::make('data.server.name')
                    ->toggleable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->name', $direction);
                    }),
                Tables\Columns\TextColumn::make('download')
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('upload')
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('ping')
                    ->toggleable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('data.download.latency.jitter')
                    ->label('Download jitter')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('data.download.latency.high')
                    ->label('Download latency high')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('data.download.latency.low')
                    ->label('Download latency low')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('data.download.latency.iqm')
                    ->label('Download latency iqm')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->iqm', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('data.upload.latency.jitter')
                    ->label('Upload jitter')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('data.upload.latency.high')
                    ->label('Upload latency high')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('data.upload.latency.low')
                    ->label('Upload latency low')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('data.upload.latency.iqm')
                    ->label('Upload latency iqm')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->iqm', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('data.ping.jitter')
                    ->label('Ping jitter')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->ping->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                Tables\Columns\TextColumn::make('packet_loss')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->packetLoss', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 2, '.', '').' %';
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('scheduled')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->tooltip(fn ($record) => $record->schedule->name ?? null)
                    ->alignment(Alignment::Center),
                Tables\Columns\IconColumn::make('healthy')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignment(Alignment::Center),
                Tables\Columns\IconColumn::make('retry')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('data.message')
                    ->label('Error Message')
                    ->limit(15)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->sortable()
                    ->alignment(Alignment::End),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignment(Alignment::End),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ip_address')
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
                Tables\Filters\SelectFilter::make('server_name')
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
                Tables\Filters\SelectFilter::make('schedule_id')
                    ->label('Schedule')
                    ->multiple()
                    ->attribute('schedule_id')
                    ->options(function (): array {
                        return Schedule::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    }),
                Tables\Filters\TernaryFilter::make('scheduled')
                    ->nullable()
                    ->trueLabel('Only scheduled speedtests')
                    ->falseLabel('Only manual speedtests')
                    ->queries(
                        true: fn (Builder $query) => $query->where('scheduled', true),
                        false: fn (Builder $query) => $query->where('scheduled', false),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options(ResultStatus::class),
                Tables\Filters\TernaryFilter::make('healthy')
                    ->nullable()
                    ->trueLabel('Only healthy speedtests')
                    ->falseLabel('Only unhealthy speedtests')
                    ->queries(
                        true: fn (Builder $query) => $query->where('healthy', true),
                        false: fn (Builder $query) => $query->where('healthy', false),
                        blank: fn (Builder $query) => $query,
                    )
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make('view result')
                        ->label('View on Speedtest.net')
                        ->icon('heroicon-o-link')
                        ->url(fn (Result $record): ?string => $record->result_url)
                        ->hidden(fn (Result $record): bool => $record->status !== ResultStatus::Completed)
                        ->openUrlInNewTab(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('updateComments')
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->hidden(fn (): bool => ! auth()->user()->is_admin && ! auth()->user()->is_user)
                        ->mountUsing(fn (Forms\ComponentContainer $form, Result $record) => $form->fill([
                            'comments' => $record->comments,
                        ]))
                        ->action(function (Result $record, array $data): void {
                            $record->comments = $data['comments'];
                            $record->save();
                        })
                        ->form([
                            Forms\Components\Textarea::make('comments')
                                ->rows(6)
                                ->maxLength(500),
                        ])
                        ->modalButton('Save'),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(ResultExporter::class)
                    ->fileName(fn (): string => 'results-'.now()->timestamp),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('truncate')
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
