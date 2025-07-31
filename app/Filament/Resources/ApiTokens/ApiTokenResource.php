<?php

namespace App\Filament\Resources\ApiTokens;

use App\Filament\Resources\ApiTokens\Pages\ListApiTokens;
use App\Filament\Resources\ApiTokens\Schemas\ApiTokenForm;
use App\Filament\Resources\ApiTokens\Tables\ApiTokenTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-api';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $label = 'API Token';

    protected static ?string $pluralLabel = 'API Tokens';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(ApiTokenForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ApiTokenTable::table($table);
    }

    public static function getTokenFormSchema(): array
    {
        return ApiTokenForm::schema();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiTokens::route('/'),
        ];
    }
}
