<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                ])->columnSpan([
                    'lg' => 2,
                ])->schema([
                    Section::make('Details')
                        ->columns([
                            'default' => 1,
                            'lg' => 2,
                        ])
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            TextInput::make('password')
                                ->confirmed()
                                ->password()
                                ->revealable()
                                ->required(fn (string $context): bool => $context === 'create')
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state)),

                            TextInput::make('password_confirmation')
                                ->password()
                                ->revealable(),

                            // ...
                        ]),
                ]),

                Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        Section::make(__('users.platform'))
                            ->schema([
                                Select::make('role')
                                    ->label(__('users.role'))
                                    ->default(UserRole::User)
                                    ->options(UserRole::class)
                                    ->required()
                                    ->disabled(fn (?User $record): bool => Auth::user()->role !== UserRole::Admin),

                                // ...
                            ]),

                        Section::make()
                            ->schema([
                                Placeholder::make('created_at')
                                    ->content(fn (?User $record): string => $record ? $record->created_at->diffForHumans() : '-'),

                                Placeholder::make('updated_at')
                                    ->content(fn (?User $record): string => $record ? $record->updated_at->diffForHumans() : '-'),

                                // ...
                            ]),
                    ]),
            ])
            ->columns([
                'default' => 1,
                'lg' => 3,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('users.id'))
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('role')
                    ->badge(),

                TextColumn::make('created_at')
                    ->alignEnd()
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->alignEnd()
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ...
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(UserRole::class),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
