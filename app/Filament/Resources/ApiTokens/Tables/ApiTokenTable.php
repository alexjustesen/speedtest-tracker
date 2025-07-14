<?php

namespace App\Filament\Resources\ApiTokens\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
                TextColumn::make('name')->searchable(),
                TextColumn::make('abilities')->badge(),
                TextColumn::make('created_at')
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('last_used_at')
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('expires_at')
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->sortable()
                    ->alignEnd(),
            ])
            ->filters([
                TernaryFilter::make('expired')
                    ->label('Token Status')
                    ->placeholder('All tokens')
                    ->falseLabel('Active tokens')
                    ->trueLabel('Expired tokens')
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
                    ->label('Abilities')
                    ->multiple()
                    ->options([
                        'results:read' => 'Read results',
                        'speedtests:run' => 'Run speedtest',
                        'ookla:list-servers' => 'List servers',
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