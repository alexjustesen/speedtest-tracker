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

    protected static ?string $label = 'API Token';

    protected static ?string $pluralLabel = 'API Tokens';

    public static function getTokenFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->required(),
                    CheckboxList::make('abilities')
                        ->label('Abilities')
                        ->options([
                            'results:read' => 'Read results',
                            'speedtests:run' => 'Run speedtest',
                            'ookla:list-servers' => 'List servers',
                        ])
                        ->required()
                        ->bulkToggleable()
                        ->descriptions([
                            'results:read' => 'Grant this token permission to read results and statistics.',
                            'speedtests:run' => 'Grant this token permission to run speedtests.',
                            'ookla:list-servers' => 'Grant this token permission to list available servers.',
                        ]),
                    DateTimePicker::make('expires_at')
                        ->label('Expires at')
                        ->nullable()
                        ->native(false)
                        ->helperText('Leave empty for no expiration'),
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
