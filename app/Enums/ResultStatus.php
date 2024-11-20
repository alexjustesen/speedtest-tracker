<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ResultStatus: string implements HasColor, HasLabel
{
    case Completed = 'completed';
    case Failed = 'failed';
    case Started = 'started';
    case Skipped = 'skipped';

    public function getColor(): ?string
    {
        return match ($this) {
            self::Completed => 'success',
            self::Failed => 'danger',
            self::Started => 'info',
            self::Skipped => 'gray',
        };
    }

    public function getLabel(): ?string
    {
        return Str::title($this->name);
    }
}
