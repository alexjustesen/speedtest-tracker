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

    protected function getPollingInterval(): ?string
    {
        return config('speedtest.dashboard_polling');
    }

    protected function getDateFilters()
    {
        return [
            '24h' => ['label' => 'Last 24h', 'date' => now()->subDay()],
            'week' => ['label' => 'Last week', 'date' => now()->subWeek()],
            'month' => ['label' => 'Last month', 'date' => now()->subMonth()],
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
