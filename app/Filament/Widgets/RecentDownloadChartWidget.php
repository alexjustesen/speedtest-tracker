<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\Number;
use App\Models\Result;
use Filament\Widgets\ChartWidget;

class RecentDownloadChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Download (Mbps)';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

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
        $results = Result::query()
            ->select(['id', 'download', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
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

        $downloads = $results->map(fn ($item) => !blank($item->download) ? Number::bitsToMagnitude(bits: $item->download_bits, precision: 2, magnitude: 'mbit') : 0);
        $averageDownload = $downloads->avg();

        return [
            'datasets' => [
                [
                    'label' => 'Download',
                    'data' => $downloads,
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => '#0ea5e9',
                    'pointBackgroundColor' => '#0ea5e9',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Average',
                    'data' => array_fill(0, count($downloads), $averageDownload),
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
