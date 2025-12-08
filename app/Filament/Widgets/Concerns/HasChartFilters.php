<?php

namespace App\Filament\Widgets\Concerns;

use Livewire\Attributes\On;

trait HasChartFilters
{
    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->initializeChartFilters();
    }

    protected function initializeChartFilters(): void
    {
        $defaultRange = config('speedtest.default_chart_range');

        $this->dateFrom = match ($defaultRange) {
            '24h' => now()->subDay()->startOfDay()->toDateTimeString(),
            'week' => now()->subWeek()->startOfDay()->toDateTimeString(),
            'month' => now()->subMonth()->startOfDay()->toDateTimeString(),
        };

        $this->dateTo = now()->endOfDay()->toDateTimeString();
    }

    #[On('date-range-updated')]
    public function updateDateRange(array $data): void
    {
        $this->dateFrom = $data['dateFrom'];
        $this->dateTo = $data['dateTo'];

        $this->updateChartData();
    }
}
