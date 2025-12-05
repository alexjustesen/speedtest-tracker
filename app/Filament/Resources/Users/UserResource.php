<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UserTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-users';

    protected static ?int $navigationSort = 4;

    public static function getLabel(): ?string
    {
        return __('general.user');
    }

    public static function getPluralLabel(): ?string
    {
        return __('general.users');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(UserForm::schema());
    }

    public static function table(Table $table): Table
    {
        return UserTable::table($table);
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
            'index' => ListUsers::route('/'),
        ];
    }
}
