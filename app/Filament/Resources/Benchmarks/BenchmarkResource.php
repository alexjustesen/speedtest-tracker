<?php

namespace App\Filament\Resources\Benchmarks;

use App\Filament\Resources\Benchmarks\Pages\ListBenchmarks;
use App\Filament\Resources\Benchmarks\Schemas\BenchmarkForm;
use App\Filament\Resources\Benchmarks\Tables\BenchmarkTable;
use App\Models\Benchmark;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BenchmarkResource extends Resource
{
    protected static ?string $model = Benchmark::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-alert-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    public static function getLabel(): ?string
    {
        return __('benchmarks.benchmark');
    }

    public static function getPluralLabel(): ?string
    {
        return __('benchmarks.benchmarks');
    }

    public static function getNavigationLabel(): string
    {
        return __('benchmarks.benchmarks');
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(BenchmarkForm::schema())->columns(1);
    }

    public static function table(Table $table): Table
    {
        return BenchmarkTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBenchmarks::route('/'),
        ];
    }
}
