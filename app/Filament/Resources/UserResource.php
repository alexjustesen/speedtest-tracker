<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                ])->columnSpan([
                    'lg' => 2,
                ])->schema([
                    Forms\Components\Section::make('Details')
                        ->columns([
                            'default' => 1,
                            'lg' => 2,
                        ])
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('password')
                                ->confirmed()
                                ->password()
                                ->revealable()
                                ->required(fn (string $context): bool => $context === 'create')
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state)),

                            Forms\Components\TextInput::make('password_confirmation')
                                ->password()
                                ->revealable(),

                            // ...
                        ]),
                ]),

                Forms\Components\Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Section::make('Platform')
                            ->schema([
                                Forms\Components\Select::make('role')
                                    ->label('Role')
                                    ->default(UserRole::User)
                                    ->options(UserRole::class)
                                    ->required()
                                    ->disabled(fn (?User $record): bool => Auth::user()->role !== UserRole::Admin),

                                // ...
                            ]),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->content(fn (?User $record): string => $record ? $record->created_at->diffForHumans() : '-'),

                                Forms\Components\Placeholder::make('updated_at')
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->alignEnd()
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->alignEnd()
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ...
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(UserRole::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
