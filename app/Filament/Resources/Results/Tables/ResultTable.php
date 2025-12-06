<?php

namespace App\Filament\Resources\Results\Tables;

use App\Enums\ResultStatus;
use App\Filament\Exports\ResultExporter;
use App\Helpers\Number;
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
use Filament\Support\Icons\Heroicon;
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
                    ->label(__('general.id'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('general.status'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('data.interface.externalIp')
                    ->label(__('results.ip_address'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('service')
                    ->label(__('results.service'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('data.server.id')
                    ->label(__('results.server_id'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->id', $direction);
                    }),

                TextColumn::make('data.server.name')
                    ->label(__('results.server_name'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->name', $direction);
                    }),

                TextColumn::make('download')
                    ->label(__('results.download'))
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : null)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                TextColumn::make('upload')
                    ->label(__('results.upload'))
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : null)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                TextColumn::make('ping')
                    ->label(__('results.ping'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),

                TextColumn::make('data.packetLoss')
                    ->label(__('results.packet_loss'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 2, '.', '').' %';
                    }),

                TextColumn::make('data.download.latency.jitter')
                    ->label(__('results.download_latency_jitter'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),

                TextColumn::make('data.upload.latency.jitter')
                    ->label(__('results.upload_latency_jitter'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' ms';
                    }),

                IconColumn::make('healthy')
                    ->label(__('general.healthy'))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedExclamationCircle)
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->alignment(Alignment::Center),

                IconColumn::make('scheduled')
                    ->label(__('results.scheduled'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),

                TextColumn::make('created_at')
                    ->label(__('general.created_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->label(__('general.created_at'))
                    ->schema([
                        DatePicker::make('created_from')
                            ->label(__('results.created_from'))
                            ->closeOnDateSelection()
                            ->native(false),
                        DatePicker::make('created_until')
                            ->label(__('results.created_until'))
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
                    ->label(__('results.ip_address'))
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
                    ->label(__('results.server_name'))
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
                    ->label(__('results.scheduled'))
                    ->nullable()
                    ->native(false)
                    ->trueLabel(__('results.only_scheduled_speedtests'))
                    ->falseLabel(__('results.only_manual_speedtests'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('scheduled', true),
                        false: fn (Builder $query) => $query->where('scheduled', false),
                        blank: fn (Builder $query) => $query,
                    ),
                SelectFilter::make('status')
                    ->label(__('general.status'))
                    ->multiple()
                    ->options(ResultStatus::class),
                TernaryFilter::make('healthy')
                    ->label(__('general.healthy'))
                    ->nullable()
                    ->native(false)
                    ->trueLabel(__('results.only_healthy_speedtests'))
                    ->falseLabel(__('results.only_unhealthy_speedtests'))
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
                        ->label(__('results.view_on_speedtest_net'))
                        ->icon('heroicon-o-link')
                        ->url(fn (Result $record): ?string => $record->result_url)
                        ->hidden(fn (Result $record): bool => $record->status !== ResultStatus::Completed)
                        ->openUrlInNewTab(),
                    Action::make('updateComments')
                        ->label(__('results.update_comments'))
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
                                ->label(__('general.comments'))
                                ->rows(6)
                                ->maxLength(500),
                        ]),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                ExportAction::make()
                    ->exporter(ResultExporter::class)
                    ->columnMapping(false)
                    ->modalHeading(__('results.export_all_results'))
                    ->modalDescription(__('results.export_all_results_description'))
                    ->fileName(fn (): string => 'results-'.now()->timestamp),
            ])
            ->defaultSort('id', 'desc')
            ->paginationPageOptions([10, 25, 50])
            ->poll('60s');
    }
}
