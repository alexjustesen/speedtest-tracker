<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServerMode: string implements HasColor, HasLabel
{
    case Auto = 'auto';
    case Prefer = 'prefer';

    public function getColor(): ?string
    {
        return match ($this) {
            self::Auto => 'gray',
            self::Prefer => 'success',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Auto => __('enums.server_mode.auto'),
            self::Prefer => __('enums.server_mode.prefer'),
        };
    }
}
