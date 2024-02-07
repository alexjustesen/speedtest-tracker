<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ResultStatus: string implements HasLabel
{
    case Completed = 'completed'; // a speedtest that ran successfully.
    case Failed = 'failed'; // a speedtest that failed to run successfully.
    case Pending = 'pending'; // a speedtest that has not been started by a worker yet.
    case Started = 'started'; // a speedtest that has been started by a worker but has not finish running.

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
