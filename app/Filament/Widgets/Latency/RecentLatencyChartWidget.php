<?php

namespace App\Filament\Widgets\Latency;

use App\Models\LatencyResult;
use Filament\Widgets\ChartWidget;

class RecentLatencyChartWidget extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    public ?string $target_url = null;

    public ?string $filter = '24h';

    protected function getPollingInterval(): ?string
    {
        return config('speedtest.dashboard_polling');
    }

    protected function getFilters(): ?array
    {
        return [
            '24h' => 'Last 24h',
            'week' => 'Last week',
            'month' => 'Last month',
        ];
    }

    protected function getData(): array
    {

        if (! $this->target_url) {

            return [];
        }

        $results = LatencyResult::query()
        ->select(['id', 'avg_latency', 'packet_loss', 'created_at'])
        ->where('target_url', $this->target_url)
        ->when($this->filter == '24h', function ($query) {
            $query->where('created_at', '>=', now()->subDay());
        })
        ->when($this->filter == 'week', function ($query) {
            $query->where('created_at', '>=', now()->subWeek());
        })
        ->when($this->filter == 'month', function ($query) {
            $query->where('created_at', '>=', now()->subMonth());
        })
        ->orderBy('created_at')
        ->get();

        // Count the number of data points
        $dataPointsCount = $results->count();

        return [
            'datasets' => [
                [
                    'label' => 'Average (ms)',
                    'data' => $results->map(fn ($item) => $item->avg_latency ?? 0)->toArray(),
                    'borderColor' => 'rgb(51, 181, 229)',
                    'backgroundColor' => 'rgba(51, 181, 229, 0.1)',
                    'pointBackgroundColor' => 'rgb(51, 181, 229)',
                    'pointRadius' => $dataPointsCount <= 5 ? 3 : 0,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Packet Loss (%)',
                    'data' => $results->map(fn ($item) => $item->packet_loss ?? 0)->toArray(),
                    'borderColor' => 'rgb(255, 87, 51)',
                    'backgroundColor' => 'rgba(255, 87, 51, 0.1)',
                    'pointBackgroundColor' => 'rgb(255, 87, 51)',
                    'pointRadius' => $dataPointsCount <= 5 ? 3 : 0,
                    'fill' => true,
                    'yAxisID' => 'right-y-axis',
                    'tension' => 0.4,
                ],
            ],

            'labels' => $results->map(fn ($item) => $item->created_at->timezone(config('app.display_timezone'))->format(config('app.chart_datetime_format')))->toArray(),
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
                    'type' => 'linear',
                    'position' => 'left',
                    'beginAtZero' => false,
                    'title' => [
                        'display' => true,
                        'text' => 'Average (ms)',
                    ],
                    'grid' => [
                        'display' => true,
                        'drawBorder' => false,
                    ],
                ],
                'right-y-axis' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Packet Loss (%)',
                    ],
                    'grid' => [
                        'display' => false,
                        'drawBorder' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): ?string
    {
        $results = LatencyResult::query()->where('target_url', $this->target_url)->first();
        $target_name = $results->target_name ?? 'Unknown';
        return $target_name;
    }
}
