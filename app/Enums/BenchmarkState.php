<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BenchmarkState: string implements HasColor, HasLabel
{
    case Ok = 'ok';
    case Alarm = 'alarm';

    public function getColor(): ?string
    {
        return match ($this) {
            self::Ok => 'success',
            self::Alarm => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Ok => __('enums.benchmark_state.ok'),
            self::Alarm => __('enums.benchmark_state.alarm'),
        };
    }
}
