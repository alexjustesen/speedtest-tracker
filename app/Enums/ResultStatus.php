<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ResultStatus: string implements HasColor, HasLabel
{
    case Completed = 'completed'; // a speedtest that ran successfully.
    case Failed = 'failed'; // a speedtest that failed to run successfully.
    case Started = 'started'; // a speedtest that has been started by a worker but has not finish running.

    public function getColor(): ?string
    {
        return match ($this) {
            self::Completed => 'success',
            self::Failed => 'danger',
            self::Started => 'warning',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Started => 'Started',
        };
    }
}
