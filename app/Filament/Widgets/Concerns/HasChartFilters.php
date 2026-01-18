<?php

namespace App\Filament\Widgets\Concerns;

use Livewire\Attributes\On;

trait HasChartFilters
{
    protected function getFilters(): ?array
    {
        return [
            '24h' => 'Last 24 hours',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            'mtd' => 'Month to date',
            '90d' => 'Last 90 days',
            'ytd' => 'Year to date',
            '365d' => 'Last 365 days',
            '5y' => 'Last 5 years',
            '10y' => 'Last 10 years',
            'all' => 'All time',
        ];
    }

    public function updatedFilter($value): void
    {
        $this->dispatch('filterChanged', filter: $value);
    }

    #[On('filterChanged')]
    public function updateFilterFromEvent($filter): void
    {
        if ($this->filter !== $filter) {
            $this->filter = $filter;
        }
    }
}
