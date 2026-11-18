<?php

namespace App\Filament\Resources\Speedtests\Tables;

use App\Actions\Schedules\ExplainCronExpression;
use App\Enums\ServerMode;
use App\Models\Speedtest;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SpeedtestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('schedule'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('schedules.columns.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('schedules.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('is_enabled')
                    ->label(__('schedules.columns.enabled'))
                    ->state(fn (Speedtest $record): bool => $record->schedule?->isEnabled() ?? false)
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('schedule.expression')
                    ->label(__('schedules.columns.schedule'))
                    ->formatStateUsing(fn (?string $state) => ExplainCronExpression::run($state))
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('schedule.next_run_at')
                    ->label(__('schedules.columns.next_run_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('schedule.last_run_at')
                    ->label(__('schedules.columns.last_run_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('server_mode')
                    ->label(__('schedules.columns.server_mode'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('servers')
                    ->label(__('schedules.columns.servers'))
                    ->state(fn (Speedtest $record): string => collect($record->serverLabels())
                        ->map(fn (string $label): string => trim(explode('(', $label)[0]))
                        ->implode(', '))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                TernaryFilter::make('enabled')
                    ->label(__('schedules.columns.enabled'))
                    ->nullable()
                    ->native(false)
                    ->trueLabel(__('schedules.filter_enabled'))
                    ->falseLabel(__('schedules.filter_disabled'))
                    ->queries(
                        true: fn (Builder $query) => $query->enabled(),
                        false: fn (Builder $query) => $query->disabled(),
                        blank: fn (Builder $query) => $query,
                    ),

                SelectFilter::make('server_mode')
                    ->label(__('schedules.columns.server_mode'))
                    ->options(ServerMode::class)
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('toggleEnabled')
                        ->label(fn (Speedtest $record): string => $record->schedule?->isEnabled()
                            ? __('schedules.action_disable')
                            : __('schedules.action_enable')
                        )
                        ->icon(fn (Speedtest $record): string => $record->schedule?->isEnabled()
                            ? 'tabler-player-pause'
                            : 'tabler-player-play'
                        )
                        ->color(fn (Speedtest $record): string => $record->schedule?->isEnabled() ? 'warning' : 'success')
                        ->action(fn (Speedtest $record) => $record->schedule?->isEnabled()
                            ? $record->schedule->disable()
                            : $record->schedule?->enable()
                        ),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('enable')
                        ->label(__('schedules.action_enable'))
                        ->icon(Heroicon::Play)
                        ->color('success')
                        ->action(fn (Collection $records) => $records->load('schedule')->each(
                            fn (Speedtest $record) => $record->schedule?->enable()
                        ))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('disable')
                        ->label(__('schedules.action_disable'))
                        ->icon(Heroicon::Pause)
                        ->color('warning')
                        ->action(fn (Collection $records) => $records->load('schedule')->each(
                            fn (Speedtest $record) => $record->schedule?->disable()
                        ))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc')
            ->paginationPageOptions([10, 25, 50])
            ->poll('60s')
            ->reorderableColumns();
    }
}
