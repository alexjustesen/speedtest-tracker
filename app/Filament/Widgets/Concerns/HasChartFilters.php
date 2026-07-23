<?php

namespace App\Filament\Widgets\Concerns;

use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Livewire\Attributes\On;

trait HasChartFilters
{
    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function mountHasChartFilters(): void
    {
        $this->dateFrom = $this->dateFrom ?? $this->defaultStartDate()->toDateTimeString();
        $this->dateTo = $this->dateTo ?? now()->toDateTimeString();
    }

    /**
     * @param  array{dateFrom: ?string, dateTo: ?string}  $data
     */
    #[On('date-range-updated')]
    public function updateDateRange(array $data): void
    {
        $this->dateFrom = $data['dateFrom'] ?? $this->dateFrom;
        $this->dateTo = $data['dateTo'] ?? $this->dateTo;

        $this->updateChartData();
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function resolveDateRange(): array
    {
        $startDate = ! blank($this->dateFrom) ? Carbon::parse($this->dateFrom) : $this->defaultStartDate();
        $endDate = ! blank($this->dateTo) ? Carbon::parse($this->dateTo) : now();

        if ($startDate->greaterThan($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [$startDate, $endDate];
    }

    protected function defaultStartDate(): Carbon
    {
        return match (app(GeneralSettings::class)->default_chart_range) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->subDay(),
        };
    }
}
