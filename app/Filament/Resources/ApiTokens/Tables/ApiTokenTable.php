<?php

namespace App\Filament\Resources\ApiTokens\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(PersonalAccessToken::query()->where('tokenable_id', Auth::id()))
            ->columns([
                TextColumn::make('name')
                    ->label(__('general.name'))
                    ->searchable(),
                TextColumn::make('abilities')
                    ->label(__('api_tokens.abilities'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('general.created_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('last_used_at')
                    ->label(__('api_tokens.last_used_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('expires_at')
                    ->label(__('api_tokens.expires_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->alignEnd(),
            ])
            ->filters([
                TernaryFilter::make('expired')
                    ->label(__('api_tokens.token_status'))
                    ->placeholder(__('api_tokens.all_tokens'))
                    ->falseLabel(__('api_tokens.active_tokens'))
                    ->trueLabel(__('api_tokens.expired_tokens'))
                    ->native(false)
                    ->queries(
                        true: fn (Builder $query) => $query
                            ->where('expires_at', '<=', now()),

                        false: fn (Builder $query) => $query
                            ->where(function (Builder $q) {
                                $q->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', now());
                            }),

                        blank: fn (Builder $query) => $query,
                    ),
                SelectFilter::make('abilities')
                    ->label(__('api_tokens.abilities'))
                    ->multiple()
                    ->options([
                        'results:read' => __('api_tokens.read_results'),
                        'speedtests:run' => __('general.run_speedtest'),
                        'ookla:list-servers' => __('general.list_servers'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        foreach ($data['values'] ?? [] as $value) {
                            $query->whereJsonContains('abilities', $value);
                        }

                        return $query;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->disabled(fn ($record) => $record->expires_at !== null && $record->expires_at->isPast())
                        ->modalWidth('xl'),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
