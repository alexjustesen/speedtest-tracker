<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Models\Result;
use Filament\Widgets\ChartWidget;

abstract class RecentChartWidget extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    public ?string $filter = '24h';

    public function mount(): void
    {
        $this->filter = config('app.chart_default_filter');
    }

    protected function getPollingInterval(): ?string
    {
        return config('speedtest.dashboard_polling');
    }

    protected function getDateFilters()
    {
        return [
            '1h' => ['label' => 'Last hour', 'date' => now()->subHour()],
            '3h' => ['label' => 'Last 3 hours', 'date' => now()->subHours(3)],
            '6h' => ['label' => 'Last 6 hours', 'date' => now()->subHours(6)],
            '12h' => ['label' => 'Last 12 hours', 'date' => now()->subHours(12)],
            '24h' => ['label' => 'Last 24 hours', 'date' => now()->subDay()],
            '1w' => ['label' => 'Last week', 'date' => now()->subWeek()],
            '1m' => ['label' => 'Last month', 'date' => now()->subMonth()],
            '3m' => ['label' => 'Last 3 months', 'date' => now()->subMonths(3)],
            '6m' => ['label' => 'Last 6 months', 'date' => now()->subMonths(6)],
            '1y' => ['label' => 'Last year', 'date' => now()->subYear()],
        ];
    }

    protected function getFilters(): ?array
    {
        return array_map(fn ($filter) => $filter['label'], $this->getDateFilters());
    }

    protected function getResults(string $column)
    {
        $filtersDates = array_map(fn ($filter) => $filter['date'], $this->getDateFilters());

        return Result::query()
            ->select(['id', $column, 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->where('created_at', '>=', $filtersDates[$this->filter])
            ->orderBy('created_at')
            ->get();
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                    'position' => 'nearest',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => config('app.chart_begin_at_zero'),
                    'grace' => 2,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
