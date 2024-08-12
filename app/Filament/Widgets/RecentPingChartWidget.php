<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Models\Result;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class RecentPingChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Ping (ms)';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    protected function getPollingInterval(): ?string
    {
        return config('speedtest.dashboard_polling');
    }

    protected function getData(): array
    {

        $startDate = $this->filters['startDate'] ?? now()->subWeek();
        $endDate = $this->filters['endDate'] ?? now();

        $results = Result::query()
            ->select(['id', 'ping', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->orderBy('created_at')
            ->get();

        $ping = $results->map(fn ($item) => ! blank($item->ping) ? number_format($item->ping, 2) : 0);
        $averagePing = $ping->avg();

        return [
            'datasets' => [
                [
                    'label' => 'Ping (ms)',
                    'data' => $ping,
                    'borderColor' => 'rgba(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'pointBackgroundColor' => 'rgba(16, 185, 129)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($ping) <= 5 ? 3 : 0,
                ],
                [
                    'label' => 'Average',
                    'data' => array_fill(0, count($ping), $averagePing),
                    'borderColor' => '#ff0000',
                    'pointBackgroundColor' => '#ff0000',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'borderDash' => [5, 5],
                    'pointRadius' => 0,
                ],
            ],
            'labels' => $results->map(fn ($item) => $item->created_at->timezone(config('app.display_timezone'))->format(config('app.chart_datetime_format'))),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
