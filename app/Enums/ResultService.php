<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ResultService: string implements HasColor, HasLabel
{
    case Ookla = 'ookla';

    public function getColor(): ?string
    {
        return match ($this) {
            self::Ookla => 'success',
        };
    }

    public function getLabel(): ?string
    {
        return Str::title($this->name);
    }
}
