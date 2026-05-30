<?php

namespace App\Filament\Resources\Schedules\Tables;

use App\Models\Schedule;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('schedules.columns.name'))
                    ->searchable()
                    ->sortable(),

                IconColumn::make('enabled')
                    ->label(__('schedules.columns.enabled'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('schedule')
                    ->label(__('schedules.columns.schedule'))
                    ->sortable(),

                TextColumn::make('server_mode')
                    ->label(__('schedules.columns.server_mode'))
                    ->state(fn (Schedule $record): string => match (true) {
                        ! blank($record->servers) => __('schedules.server_mode_options.prefer'),
                        ! blank($record->blocked_servers) => __('schedules.server_mode_options.block'),
                        default => __('schedules.server_mode_options.auto'),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        __('schedules.server_mode_options.prefer') => 'success',
                        __('schedules.server_mode_options.block') => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('servers')
                    ->label(__('schedules.columns.servers'))
                    ->state(fn (Schedule $record): string => implode(', ', $record->servers ?? $record->blocked_servers ?? []))
                    ->placeholder('—'),

                TextColumn::make('interface')
                    ->label(__('schedules.columns.interface'))
                    ->placeholder('—'),

                TextColumn::make('skip_ips')
                    ->label(__('schedules.columns.skip_ips'))
                    ->state(fn (Schedule $record): string => implode(', ', $record->skip_ips ?? []))
                    ->placeholder('—'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
