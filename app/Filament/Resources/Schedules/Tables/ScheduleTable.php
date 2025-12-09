<?php

namespace App\Filament\Resources\Schedules\Tables;

use App\Actions\ExplainCronExpression;
use App\Enums\ScheduleStatus;
use App\Models\Schedule;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScheduleTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('schedules.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('schedules.name'))
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('schedules.type'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('schedules.description'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('schedule')
                    ->label(__('schedules.schedule'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn (?string $state) => ExplainCronExpression::run($state)),
                TextColumn::make('options.server_preference')
                    ->label(__('schedules.server_preference'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function (?string $state) {
                        return match ($state) {
                            'auto' => __('schedules.server_preference_auto'),
                            'prefer' => __('schedules.server_preference_prefer'),
                            'ignore' => __('schedules.server_preference_ignore'),
                            default => $state,
                        };
                    })
                    ->tooltip(fn ($record) => $record->getServerTooltip()),
                IconColumn::make('is_active')
                    ->label(__('schedules.active'))
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->boolean(),
                TextColumn::make('status')
                    ->label(__('schedules.status'))
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('next_run_at')
                    ->label(__('schedules.next_run_at'))
                    ->alignEnd()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('last_run_at')
                    ->label(__('schedules.last_run_at'))
                    ->alignEnd()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('schedules.created_by'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('general.created_at'))
                    ->alignEnd()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('schedules.type'))
                    ->options(function () {
                        return Schedule::distinct()
                            ->pluck('type', 'type')
                            ->toArray();
                    })
                    ->native(false),
                TernaryFilter::make('Active')
                    ->label(__('schedules.active'))
                    ->nullable()
                    ->trueLabel(__('schedules.active_schedules_only'))
                    ->falseLabel(__('schedules.inactive_schedules_only'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                        blank: fn (Builder $query) => $query,
                    )
                    ->native(false),
                SelectFilter::make('options.server_preference')
                    ->label(__('schedules.server_preference'))
                    ->options(function () {
                        return Schedule::query()
                            ->get()
                            ->pluck('options')
                            ->map(function ($options) {
                                return $options['server_preference'] ?? null;
                            })
                            ->filter()
                            ->unique()
                            ->mapWithKeys(function ($value) {
                                return [
                                    $value => match ($value) {
                                        'auto' => __('schedules.server_preference_auto'),
                                        'prefer' => __('schedules.server_preference_prefer'),
                                        'ignore' => __('schedules.server_preference_ignore'),
                                        default => $value,
                                    },
                                ];
                            })
                            ->toArray();
                    })
                    ->native(false),
                SelectFilter::make('status')
                    ->label(__('schedules.status'))
                    ->options(ScheduleStatus::class)
                    ->native(false),
                SelectFilter::make('created_by')
                    ->label(__('schedules.created_by'))
                    ->options(function () {
                        return User::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('changeScheduleStatus')
                        ->label(__('schedules.change_schedule_status'))
                        ->action(function ($record) {
                            $record->update(['is_active' => ! $record->is_active]);
                        })
                        ->icon('heroicon-c-arrow-path'),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->poll('60s');
    }
}
