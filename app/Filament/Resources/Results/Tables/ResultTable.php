<?php

namespace App\Filament\Resources\Results\Tables;

use App\Filament\Exports\ResultExporter;
use App\Jobs\TruncateResults;
use App\Models\Result;
use App\Helpers\Number;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use App\Enums\ResultStatus;

class ResultTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable()
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
                    ->toggleable()                    
                    ->sortable(),
                TextColumn::make('upload')
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : null)
                    ->toggleable()
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
                TextColumn::make('data.packetLoss')
                    ->label('Packet Loss')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 2, '.', '').' %';
                    }),
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
                    ->schema([
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
                    ->options([]), // You may want to fill this in
                TernaryFilter::make('status')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false)
                    ->queries(
                        true: fn (Builder $query) => $query->where('status', ResultStatus::Completed),
                        false: fn (Builder $query) => $query->where('status', ResultStatus::Failed),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                ExportAction::make()
                    ->exporter(ResultExporter::class)
                    ->columnMapping(false)
                    ->modalHeading('Export all Results')
                    ->modalDescription('This will export all columns for all results.')
                    ->fileName(fn (): string => 'results-'.now()->timestamp),
                Action::make('truncate')
                    ->action(fn () => TruncateResults::dispatch(Auth::user()))
                    ->requiresConfirmation()
                    ->modalHeading('Truncate Results')
                    ->modalDescription('Are you sure you want to truncate all results data? This can\'t be undone.')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->hidden(fn (): bool => ! Auth::user()->is_admin),
            ])
            ->defaultSort('id', 'desc')
            ->deferLoading()
            ->poll('60s');
    }
} 