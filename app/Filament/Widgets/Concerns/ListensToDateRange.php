<?php

namespace App\Filament\Widgets\Concerns;

use Livewire\Attributes\On;

trait ListensToDateRange
{
    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    #[On('date-range-updated')]
    public function updateDateRange(array $data): void
    {
        $this->dateFrom = $data['dateFrom'];
        $this->dateTo = $data['dateTo'];

        $this->updateChartData();
    }
}
