<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Models\Result;
use Filament\Widgets\ChartWidget;

class RecentUploadLatencyChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Upload Latency';

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
            ->select(['id', 'data', 'created_at'])
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

        return [
            'datasets' => [
                [
                    'label' => 'Average (ms)',
                    'data' => $results->map(fn ($item) => $item->upload_latency_iqm ? number_format($item->upload_latency_iqm, 2) : 0),
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b981',
                    'pointBackgroundColor' => '#10b981',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'High (ms)',
                    'data' => $results->map(fn ($item) => $item->upload_latency_high ? number_format($item->upload_latency_high, 2) : 0),
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => '#0ea5e9',
                    'pointBackgroundColor' => '#0ea5e9',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Low (ms)',
                    'data' => $results->map(fn ($item) => $item->upload_latency_low ? number_format($item->upload_latency_low, 2) : 0),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => '#8b5cf6',
                    'pointBackgroundColor' => '#8b5cf6',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $results->map(fn ($item) => $item->created_at->timezone(config('app.display_timezone'))->format(config('app.chart_datetime_format'))),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
