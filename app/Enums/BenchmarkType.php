<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BenchmarkType: string implements HasLabel
{
    case Absolute = 'absolute';
    case Relative = 'relative';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Absolute => __('enums.benchmark_type.absolute'),
            self::Relative => __('enums.benchmark_type.relative'),
        };
    }
}
