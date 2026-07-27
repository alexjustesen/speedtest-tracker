<?php

namespace App\Filament\Resources\Speedtests\Tables;

use App\Actions\Schedules\ExplainCronExpression;
use App\Models\Speedtest;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SpeedtestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('schedules'))
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
                    ->state(fn (Speedtest $record): bool => $record->isEnabled)
                    ->boolean()
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('schedule')
                    ->label(__('schedules.columns.schedule'))
                    ->state(fn (Speedtest $record): ?string => $record->cronExpression)
                    ->formatStateUsing(fn (?string $state) => ExplainCronExpression::run($state))
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('next_run_at')
                    ->label(__('schedules.columns.next_run_at'))
                    ->state(fn (Speedtest $record) => $record->isEnabled ? $record->nextRunAt : null)
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->placeholder('—')
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('last_run_at')
                    ->label(__('schedules.columns.last_run_at'))
                    ->state(fn (Speedtest $record) => $record->lastRunAt)
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->placeholder('—')
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('server_mode')
                    ->label(__('schedules.columns.server_mode'))
                    ->state(fn (Speedtest $record): string => match (true) {
                        ! blank($record->servers) => 'prefer',
                        ! blank($record->blocked_servers) => 'block',
                        default => 'auto',
                    })
                    ->formatStateUsing(fn (string $state): string => __("schedules.server_mode_options.{$state}"))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prefer' => 'success',
                        'block' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('servers')
                    ->label(__('schedules.columns.servers'))
                    ->state(function (Speedtest $record): string {
                        $ids = $record->servers ?? $record->blocked_servers ?? [];
                        $labels = $record->server_labels ?? [];

                        return implode(', ', array_map(function ($id) use ($labels) {
                            $label = $labels[$id] ?? $id;

                            return trim(explode('(', $label)[0]);
                        }, $ids));
                    })
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('interface')
                    ->label(__('schedules.columns.interface'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('skip_ips')
                    ->label(__('schedules.columns.skip_ips'))
                    ->state(fn (Speedtest $record): string => implode(', ', $record->skip_ips ?? []))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
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

                Filter::make('server_mode')
                    ->label(__('schedules.columns.server_mode'))
                    ->schema([
                        Select::make('value')
                            ->label(__('schedules.columns.server_mode'))
                            ->options(__('schedules.server_mode_options'))
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'prefer' => $query->whereNotNull('servers'),
                        'block' => $query->whereNull('servers')->whereNotNull('blocked_servers'),
                        'auto' => $query->whereNull('servers')->whereNull('blocked_servers'),
                        default => $query,
                    })
                    ->indicateUsing(fn (array $data): ?string => filled($data['value'] ?? null)
                        ? __('schedules.columns.server_mode').': '.__("schedules.server_mode_options.{$data['value']}")
                        : null
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('toggleEnabled')
                        ->label(fn (Speedtest $record): string => $record->isEnabled
                            ? __('schedules.action_disable')
                            : __('schedules.action_enable')
                        )
                        ->icon(fn (Speedtest $record): string => $record->isEnabled
                            ? 'tabler-player-pause'
                            : 'tabler-player-play'
                        )
                        ->color(fn (Speedtest $record): string => $record->isEnabled ? 'warning' : 'success')
                        ->action(fn (Speedtest $record) => $record->isEnabled
                            ? $record->cronSchedule()?->disable()
                            : $record->cronSchedule()?->enable()
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
                        ->action(fn (Collection $records) => $records->each(
                            fn (Speedtest $record) => $record->cronSchedule()?->enable()
                        ))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('disable')
                        ->label(__('schedules.action_disable'))
                        ->icon(Heroicon::Pause)
                        ->color('warning')
                        ->action(fn (Collection $records) => $records->each(
                            fn (Speedtest $record) => $record->cronSchedule()?->disable()
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
