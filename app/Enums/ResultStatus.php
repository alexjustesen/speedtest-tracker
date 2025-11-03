<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ResultStatus: string implements HasColor, HasLabel
{
    case Benchmarking = 'benchmarking';
    case Checking = 'checking';
    case Completed = 'completed';
    case Failed = 'failed';
    case Running = 'running';
    case Started = 'started';
    case Skipped = 'skipped';
    case Waiting = 'waiting';

    public function getColor(): ?string
    {
        return match ($this) {
            self::Benchmarking => 'info',
            self::Checking => 'info',
            self::Completed => 'success',
            self::Failed => 'danger',
            self::Running => 'info',
            self::Started => 'info',
            self::Skipped => 'gray',
            self::Waiting => 'info',
        };
    }

    public function getLabel(): ?string
    {
        return Str::title(__(''.$this->name));
    }
}
