<?php

namespace App\Filament\Widgets;

class RecentDownloadLatencyChartWidget extends RecentChartWidget
{
    protected static ?string $heading = 'Download Latency';

    protected function getData(): array
    {
        $results = $this->getResults('data');

        return [
            'datasets' => [
                [
                    'label' => 'Average (ms)',
                    'data' => $results->map(fn ($item) => $item->download_latency_iqm),
                    'borderColor' => 'rgba(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'pointBackgroundColor' => 'rgba(16, 185, 129)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
                [
                    'label' => 'High (ms)',
                    'data' => $results->map(fn ($item) => $item->download_latency_high),
                    'borderColor' => 'rgba(14, 165, 233)',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'pointBackgroundColor' => 'rgba(14, 165, 233)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
                [
                    'label' => 'Low (ms)',
                    'data' => $results->map(fn ($item) => $item->download_latency_low),
                    'borderColor' => 'rgba(139, 92, 246)',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'pointBackgroundColor' => 'rgba(139, 92, 246)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
            ],
            'labels' => $results->map(fn ($item) => $item->created_at->timezone(config('app.display_timezone'))->format(config('app.chart_datetime_format'))),
        ];
    }
}
