<?php

namespace App\Filament\Resources\Schedules\Tables;

use App\Actions\Schedules\ExplainCronExpression;
use App\Models\Schedule;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
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
                    ->formatStateUsing(fn (?string $state) => ExplainCronExpression::run($state))
                    ->sortable(),

                TextColumn::make('next_run_at')
                    ->label(__('schedules.columns.next_run_at'))
                    ->state(function (Schedule $record): ?Carbon {
                        if (! $record->enabled) {
                            return null;
                        }

                        $cron = new CronExpression($record->schedule);

                        return Carbon::parse(
                            $cron->getNextRunDate(timeZone: config('app.display_timezone'))
                        );
                    })
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->placeholder('—')
                    ->sortable(false),

                TextColumn::make('server_mode')
                    ->label(__('schedules.columns.server_mode'))
                    ->state(fn (Schedule $record): string => match (true) {
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
                    }),

                TextColumn::make('servers')
                    ->label(__('schedules.columns.servers'))
                    ->state(function (Schedule $record): string {
                        $ids = $record->servers ?? $record->blocked_servers ?? [];
                        $labels = $record->server_labels ?? [];

                        return implode(', ', array_map(function ($id) use ($labels) {
                            $label = $labels[$id] ?? $id;

                            return trim(explode('(', $label)[0]);
                        }, $ids));
                    })
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
                TernaryFilter::make('enabled')
                    ->label(__('schedules.columns.enabled'))
                    ->nullable()
                    ->native(false)
                    ->trueLabel(__('schedules.filter_enabled'))
                    ->falseLabel(__('schedules.filter_disabled'))
                    ->queries(
                        true: fn ($query) => $query->where('enabled', true),
                        false: fn ($query) => $query->where('enabled', false),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('toggleEnabled')
                        ->label(fn (Schedule $record): string => $record->enabled
                            ? __('schedules.action_disable')
                            : __('schedules.action_enable')
                        )
                        ->icon(fn (Schedule $record): string => $record->enabled
                            ? 'tabler-player-pause'
                            : 'tabler-player-play'
                        )
                        ->color(fn (Schedule $record): string => $record->enabled ? 'warning' : 'success')
                        ->action(fn (Schedule $record) => $record->update(['enabled' => ! $record->enabled])),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('60s');
    }
}
