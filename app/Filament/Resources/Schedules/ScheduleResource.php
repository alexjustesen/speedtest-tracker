<?php

namespace App\Filament\Resources\Schedules;

use App\Filament\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\Resources\Schedules\Pages\EditSchedule;
use App\Filament\Resources\Schedules\Pages\ListSchedules;
use App\Filament\Resources\Schedules\Schemas\ScheduleForm;
use App\Filament\Resources\Schedules\Tables\ScheduleTable;
use App\Models\Schedule;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    public static function getNavigationLabel(): string
    {
        return 'Schedules';
    }

    public static function getModelLabel(): string
    {
        return 'Schedule';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Schedules';
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
        return $schema->components(ScheduleForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ScheduleTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchedules::route('/'),
           # 'create' => CreateSchedule::route('/create'),
           # 'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }
}
