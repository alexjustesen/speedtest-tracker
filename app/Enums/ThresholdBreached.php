<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ThresholdBreached: string implements HasColor, HasLabel
{
    case Failed = 'Failed';
    case Passed = 'Passed';
    case Unknown = 'Unknown';

    public function getColor(): ?string
    {
        return match ($this) {
            self::Failed => 'danger',
            self::Passed => 'success',
            self::Unknown => 'warning',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Failed => 'Failed',
            self::Passed => 'Passed',
            self::Unknown => 'Unknown',
        };
    }
}
