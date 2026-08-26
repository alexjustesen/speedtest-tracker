<?php

namespace App\Filament\Resources\Benchmarks\Schemas;

use App\Enums\BenchmarkType;
use App\Models\Benchmark;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;

class BenchmarkForm
{
    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function schema(): array
    {
        return [
            Toggle::make('enabled')
                ->label(__('general.enable'))
                ->live()
                ->columnSpanFull(),

            Select::make('type')
                ->label(__('benchmarks.type'))
                ->options(BenchmarkType::class)
                ->native(false)
                ->live()
                ->required()
                ->columnSpanFull(),

            TextInput::make('absolute_value')
                ->label(__('benchmarks.absolute_value'))
                ->hint(fn (?Benchmark $record) => $record?->metric?->unit())
                ->helperText(__('benchmarks.absolute_value_helper'))
                ->numeric()
                ->minValue(0)
                ->required()
                ->visible(fn (Get $get) => self::isType($get, BenchmarkType::Absolute))
                ->columnSpanFull(),

            Fieldset::make(__('benchmarks.relative'))
                ->visible(fn (Get $get) => self::isType($get, BenchmarkType::Relative))
                ->schema([
                    TextInput::make('baseline_value')
                        ->label(__('benchmarks.baseline_value'))
                        ->hint(fn (?Benchmark $record) => $record?->metric?->unit())
                        ->helperText(__('benchmarks.baseline_value_helper'))
                        ->numeric()
                        ->minValue(0)
                        ->required(fn (Get $get) => self::isType($get, BenchmarkType::Relative)),
                    TextInput::make('relative_percentage')
                        ->label(__('benchmarks.relative_percentage'))
                        ->hint('%')
                        ->helperText(__('benchmarks.relative_percentage_helper'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(fn (Get $get) => self::isType($get, BenchmarkType::Relative)),
                ]),

            Fieldset::make(__('benchmarks.debounce'))
                ->schema([
                    TextInput::make('consecutive_breaches')
                        ->label(__('benchmarks.consecutive_breaches'))
                        ->helperText(__('benchmarks.consecutive_breaches_helper'))
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required(),
                    Checkbox::make('repeat_while_in_alarm')
                        ->label(__('benchmarks.repeat_while_in_alarm'))
                        ->helperText(__('benchmarks.repeat_while_in_alarm_helper')),
                ]),
        ];
    }

    /**
     * Compare the form's current `type` state against a benchmark type.
     *
     * The `type` field's state is the raw enum instance when hydrated from
     * the record, but a plain string once the user changes the select, so
     * both forms need to be handled here.
     */
    private static function isType(Get $get, BenchmarkType $type): bool
    {
        $state = $get('type');

        return ($state instanceof BenchmarkType ? $state->value : $state) === $type->value;
    }
}
