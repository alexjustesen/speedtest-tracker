<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\Number;
use App\Models\Result;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class RecentDownloadChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Download (Mbps)';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    protected function getPollingInterval(): ?string
    {
        return config('speedtest.dashboard_polling');
    }

    protected function getData(): array
    {
        // Ensure that startDate and endDate are treated as Carbon instances
        $startDate = $this->filters['startDate'] ?? now()->subWeek();
        $endDate = $this->filters['endDate'] ?? now();

        // Convert dates to the correct timezone if necessary
        $startDate = \Carbon\Carbon::parse($startDate)->startOfDay()->timezone(config('app.timezone'));
        $endDate = \Carbon\Carbon::parse($endDate)->endOfDay()->timezone(config('app.timezone'));

        $results = Result::query()
            ->select(['id', 'download', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();

        $downloads = $results->map(fn ($item) => ! blank($item->download) ? Number::bitsToMagnitude(bits: $item->download_bits, precision: 2, magnitude: 'mbit') : 0);
        $averageDownload = $downloads->avg();

        return [
            'datasets' => [
                [
                    'label' => 'Download',
                    'data' => $downloads,
                    'borderColor' => 'rgba(14, 165, 233)',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',  // 10% opacity
                    'pointBackgroundColor' => 'rgba(14, 165, 233)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($downloads) <= 5 ? 3 : 0,
                ],
                [
                    'label' => 'Average',
                    'data' => array_fill(0, count($downloads), $averageDownload),
                    'borderColor' => 'rgb(255, 165, 0)',
                    'pointBackgroundColor' => 'rgb(255, 165, 0)',
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
                    'beginAtZero' => config('app.chart_begin_at_zero'),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
