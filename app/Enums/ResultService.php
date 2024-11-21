<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ResultService: string implements HasLabel
{
    case Ookla = 'ookla';

    public function getLabel(): ?string
    {
        return Str::title($this->name);
    }
}
