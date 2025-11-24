<?php

namespace App\Filament\Resources\Schedules\Tables;

use App\Actions\ExplainCronExpression;
use App\Models\Schedule;
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
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('token')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name'),
                TextColumn::make('type')
                    ->label('Type')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('description')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('options.cron_expression')
                    ->label('Schedule')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn (?string $state) => ExplainCronExpression::run($state)),
                TextColumn::make('options.server_preference')
                    ->label('Server Preference')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function (?string $state) {
                        return match ($state) {
                            'auto' => 'Automatic',
                            'prefer' => 'Prefer Specific Servers',
                            'ignore' => 'Ignore Specific Servers',
                            default => $state,
                        };
                    })
                    ->tooltip(fn ($record) => $record->getServerTooltip()),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->boolean(),
                TextColumn::make('next_run_at')
                    ->alignEnd()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->alignEnd()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->deferFilters(false)
            ->deferColumnManager(false)
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(function () {
                        return Schedule::distinct()
                            ->pluck('type', 'type')
                            ->toArray();
                    })
                    ->native(false),
                TernaryFilter::make('Active')
                    ->nullable()
                    ->trueLabel('Active schedules only')
                    ->falseLabel('Inactive schedules only')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                        blank: fn (Builder $query) => $query,
                    )
                    ->native(false),
                SelectFilter::make('options.server_preference')
                    ->label('Server Preference')
                    ->options(function () {
                        return Schedule::distinct()
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
                                        'auto' => 'Automatic',
                                        'prefer' => 'Prefer Specific Servers',
                                        'ignore' => 'Ignore Specific Servers',
                                        default => $value,
                                    },
                                ];
                            })
                            ->toArray();
                    })
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('changeScheduleStatus')
                        ->label('Change Schedule Status')
                        ->action(function ($record) {
                            $record->update(['is_active' => ! $record->is_active]);
                        })
                        ->icon('heroicon-c-arrow-path'),
                    Action::make('viewResults')
                        ->label('View Results')
                        ->action(function ($record) {
                            return redirect()->route('filament.admin.resources.results.index', [
                                'tableFilters[schedule_id][values][0]' => $record->id,
                            ]);
                        })
                        ->icon('heroicon-s-eye'),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->poll('60s');
    }
}
