<?php

namespace App\Filament\Resources\Benchmarks\Tables;

use App\Models\Benchmark;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BenchmarkTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('metric')
                    ->label(__('benchmarks.metric')),
                TextColumn::make('type')
                    ->label(__('benchmarks.type'))
                    ->badge(),
                TextColumn::make('benchmark_value')
                    ->label(__('benchmarks.benchmark_value'))
                    ->getStateUsing(function (Benchmark $record): string {
                        $value = $record->benchmarkValue();

                        return $value !== null ? $value.' '.$record->metric->unit() : '—';
                    }),
                ToggleColumn::make('enabled')
                    ->label(__('general.enable'))
                    ->disabled(fn (Benchmark $record): bool => $record->benchmarkValue() === null)
                    ->tooltip(fn (Benchmark $record): ?string => $record->benchmarkValue() === null
                        ? __('benchmarks.enable_requires_value')
                        : null),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->paginated(false);
    }
}
