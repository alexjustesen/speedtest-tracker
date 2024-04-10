<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Helpers\TimeZoneHelper;
use App\Models\User;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;

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
                    'md' => 3,
                ])
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->email()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('password')
                                    ->required()
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->visible(fn ($livewire) => $livewire instanceof CreateUser)
                                    ->rule(Password::default()),
                                Forms\Components\TextInput::make('new_password')
                                    ->password()
                                    ->label('New Password')
                                    ->nullable()
                                    ->rule(Password::default())
                                    ->visible(fn ($livewire) => $livewire instanceof EditUser)
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('new_password_confirmation')
                                    ->password()
                                    ->label('Confirm New Password')
                                    ->rule('required', fn ($get) => (bool) $get('new_password'))
                                    ->same('new_password')
                                    ->visible(fn ($livewire) => $livewire instanceof EditUser)
                                    ->dehydrated(false),
                            ])
                            ->columns(1)
                            ->columnSpan([
                                'md' => 2,
                            ]),

                        Forms\Components\Grid::make([
                            'default' => 1,
                        ])
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('role')
                                            ->options(UserRole::class)
                                            ->disabled(fn (): bool => ! Auth::user()->is_admin)
                                            ->required(),
                                    ])
                                    ->columns(1)
                                    ->columnSpan([
                                        'md' => 1,
                                    ]),

                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('created_at')
                                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                                        Forms\Components\Placeholder::make('updated_at')
                                            ->content(fn ($record) => $record?->updated_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                                    ])
                                    ->columns(1)
                                    ->columnSpan([
                                        'md' => 1,
                                    ]),
                            ])
                            ->columns(1)
                            ->columnSpan([
                                'md' => 1,
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $settings = new GeneralSettings();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(UserRole::class),
                Tables\Columns\TextColumn::make('created_at')
                    ->alignEnd()
                    ->dateTime($settings->time_format ?? 'M j, Y G:i:s')
                    ->timezone(TimeZoneHelper::displayTimeZone($settings)),
                Tables\Columns\TextColumn::make('updated_at')
                    ->alignEnd()
                    ->dateTime($settings->time_format ?? 'M j, Y G:i:s')
                    ->timezone(TimeZoneHelper::displayTimeZone($settings))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(UserRole::class),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
