<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ResultStatus: string implements HasLabel, HasColor
{
    case Completed = 'completed'; // a speedtest that ran successfully.
    case Failed = 'failed'; // a speedtest that failed to run successfully.
    case Started = 'started'; // a speedtest that has been started by a worker but has not finish running.

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): ?string
    {
        return match ($this) {
            ResultStatus::Completed => 'success',
            ResultStatus::Failed => 'danger',
            ResultStatus::Started => 'warning',
        };
    }
}
