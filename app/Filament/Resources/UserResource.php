<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'system/users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                'left' => Card::make([
                    'name' => TextInput::make('name')
                        ->required(),
                    'email' => TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(ignoreRecord: true),
                    'password' => TextInput::make('password')
                        ->required()
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->visible(fn ($livewire) => $livewire instanceof CreateUser)
                        ->rule(Password::default()),
                    'new_password_group' => Group::make([
                        'new_password' => TextInput::make('new_password')
                            ->password()
                            ->label('New Password')
                            ->nullable()
                            ->rule(Password::default())
                            ->visible(fn ($livewire) => $livewire instanceof EditUser)
                            ->dehydrated(false),
                        'new_password_confirmation' => TextInput::make('new_password_confirmation')
                            ->password()
                            ->label('Confirm New Password')
                            ->rule('required', fn ($get) => (bool) $get('new_password'))
                            ->same('new_password')
                            ->visible(fn ($livewire) => $livewire instanceof EditUser)
                            ->dehydrated(false),
                    ]),
                ])->columnSpan(8),
                'right' => Card::make([
                    'created_at' => Placeholder::make('created_at')
                        ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                ])->columnSpan(4),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('email_verified_at')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
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
