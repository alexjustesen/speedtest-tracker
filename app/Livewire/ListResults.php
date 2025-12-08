<?php

namespace App\Livewire;

use App\Filament\Tables\Columns\ResultServerColumn;
use App\Helpers\Number;
use App\Models\Result;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListResults extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions, InteractsWithSchemas, InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Result::query())
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

                ResultServerColumn::make('server')
                    ->label(__('general.server'))
                    ->toggleable(isToggledHiddenByDefault: false),

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
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn (Result $record): bool => Auth::user()?->cannot('view', $record) ?? true),
                    DeleteAction::make()
                        ->hidden(fn (Result $record): bool => Auth::user()?->cannot('delete', $record) ?? true),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->hidden(fn (): bool => Auth::user()?->cannot('deleteAny', Result::class) ?? true),
            ])
            ->defaultSort('id', 'desc')
            ->paginationPageOptions([10, 25, 50])
            ->poll('60s');
    }

    public function render()
    {
        return view('livewire.list-results');
    }
}
