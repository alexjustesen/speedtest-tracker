<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiTokenResource\Pages;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static ?string $navigationIcon = 'tabler-api';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $label = null;

    protected static ?string $pluralLabel = null;

    public static function getLabel(): ?string
    {
        return __('translations.api.api_token');
    }

    public static function getPluralLabel(): ?string
    {
        return __('translations.api.api_tokens');
    }

    public static function getTokenFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('name')
                        ->label(__('translations.api.name'))
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->required(),
                    CheckboxList::make('abilities')
                        ->label(__('translations.api.abilities'))
                        ->options([
                            'results:read' => __('translations.api.read_results'),
                            'speedtests:run' => __('translations.api.run_speedtest'),
                            'ookla:list-servers' => __('translations.api.list_servers'),
                        ])
                        ->required()
                        ->bulkToggleable()
                        ->descriptions([
                            'results:read' => __('translations.api.read_results_description'),
                            'speedtests:run' => __('translations.api.run_speedtest_description'),
                            'ookla:list-servers' => __('translations.api.list_servers_description'),
                        ]),
                    DateTimePicker::make('expires_at')
                        ->label(__('translations.api.expires_at'))
                        ->nullable()
                        ->native(false)
                        ->helperText(__('translations.api.expires_at_helper')),
                ])
                ->columns([
                    'lg' => 1,
                ]),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(static::getTokenFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(PersonalAccessToken::query()->where('tokenable_id', Auth::id()))
            ->columns([
                TextColumn::make('name')
                    ->label(__('translations.api.name'))
                    ->searchable(),
                TextColumn::make('abilities')
                    ->label(__('translations.api.abilities'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('translations.api.created_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('last_used_at')
                    ->label(__('translations.api.last_used_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('expires_at')
                    ->label(__('translations.api.expires_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->toggleable()
                    ->sortable()
                    ->alignEnd(),
            ])
            ->filters([
                TernaryFilter::make('expired')
                    ->label(__('translations.api.token_status'))
                    ->placeholder(__('translations.api.all_tokens'))
                    ->falseLabel(__('translations.api.active_tokens'))
                    ->trueLabel(__('translations.api.expired_tokens'))
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
                    ->label(__('translations.api.abilities'))
                    ->multiple()
                    ->options([
                        'results:read' => __('translations.api.read_results'),
                        'speedtests:run' => __('translations.api.run_speedtest'),
                        'ookla:list-servers' => __('translations.api.list_servers'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        foreach ($data['values'] ?? [] as $value) {
                            $query->whereJsonContains('abilities', $value);
                        }

                        return $query;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->disabled(fn ($record) => $record->expires_at !== null && $record->expires_at->isPast())
                        ->modalWidth('xl'),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiTokens::route('/'),
        ];
    }
}
