<?php

namespace App\Filament\Resources\Speedtests;

use App\Filament\Resources\Speedtests\Pages\CreateSpeedtest;
use App\Filament\Resources\Speedtests\Pages\EditSpeedtest;
use App\Filament\Resources\Speedtests\Pages\ListSpeedtests;
use App\Filament\Resources\Speedtests\Schemas\SpeedtestForm;
use App\Filament\Resources\Speedtests\Tables\SpeedtestsTable;
use App\Models\Speedtest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SpeedtestResource extends Resource
{
    protected static ?string $model = Speedtest::class;

    protected static string|BackedEnum|null $navigationIcon = 'tabler-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('schedules.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('schedules.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('schedules.label');
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function form(Schema $schema): Schema
    {
        return SpeedtestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpeedtestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpeedtests::route('/'),
            'create' => CreateSpeedtest::route('/create'),
            'edit' => EditSpeedtest::route('/{record}/edit'),
        ];
    }
}
