<?php

namespace App\Filament\Resources\Results\Tables;

use App\Enums\ResultStatus;
use App\Filament\Exports\ResultExporter;
use App\Helpers\Number;
use App\Jobs\TruncateResults;
use App\Models\Result;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ResultTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('data.interface.externalIp')
                    ->label('IP address')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->interface->externalIp', $direction);
                    }),
                TextColumn::make('service')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('data.server.id')
                    ->label('Server ID')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->id', $direction);
                    }),
                TextColumn::make('data.isp')
                    ->label('ISP')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->isp', $direction);
                    }),
                TextColumn::make('data.server.location')
                    ->label('Server Location')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->location', $direction);
                    }),
                TextColumn::make('data.server.name')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->name', $direction);
                    }),
                TextColumn::make('download')
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : null)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('upload')
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : null)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('ping')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.download.latency.jitter')
                    ->label('Download jitter')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.download.latency.high')
                    ->label('Download latency high')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.download.latency.low')
                    ->label('Download latency low')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.download.latency.iqm')
                    ->label('Download latency iqm')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->iqm', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.upload.latency.jitter')
                    ->label('Upload jitter')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.upload.latency.high')
                    ->label('Upload latency high')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.upload.latency.low')
                    ->label('Upload latency low')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.upload.latency.iqm')
                    ->label('Upload latency iqm')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->iqm', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),
                TextColumn::make('data.packetLoss')
                    ->label('Packet Loss')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 2, '.', '').' %';
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                IconColumn::make('scheduled')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),
                IconColumn::make('healthy')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->alignment(Alignment::Center),
                TextColumn::make('data.message')
                    ->label('Error Message')
                    ->limit(15)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->alignment(Alignment::End),
                TextColumn::make('updated_at')
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->alignment(Alignment::End),
            ])
            ->deferFilters(false)
            ->deferColumnManager(false)
            ->filters([
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->closeOnDateSelection()
                            ->native(false),
                        DatePicker::make('created_until')
                            ->closeOnDateSelection()
                            ->native(false),
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
                    ->native(false)
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
                    ->native(false)
                    ->trueLabel('Only healthy speedtests')
                    ->falseLabel('Only unhealthy speedtests')
                    ->queries(
                        true: fn (Builder $query) => $query->where('healthy', true),
                        false: fn (Builder $query) => $query->where('healthy', false),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make(),
                    Action::make('view result')
                        ->label('View on Speedtest.net')
                        ->icon('heroicon-o-link')
                        ->url(fn (Result $record): ?string => $record->result_url)
                        ->hidden(fn (Result $record): bool => $record->status !== ResultStatus::Completed)
                        ->openUrlInNewTab(),
                    Action::make('updateComments')
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->hidden(fn (): bool => ! (Auth::user()?->is_admin ?? false) && ! (Auth::user()?->is_user ?? false))
                        ->mountUsing(fn ($form, Result $record) => $form->fill([
                            'comments' => $record->comments,
                        ]))
                        ->action(function (Result $record, array $data): void {
                            $record->comments = $data['comments'];
                            $record->save();
                        })
                        ->schema([
                            Textarea::make('comments')
                                ->rows(6)
                                ->maxLength(500),
                        ]),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ResultExporter::class)
                    ->columnMapping(false)
                    ->modalHeading('Export all Results')
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
                ])
                ->dropdownPlacement('left-start'),
            ])
            ->defaultSort('id', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 'all'])
            ->deferLoading()
            ->poll('60s');
    }
}
