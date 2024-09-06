<?php

namespace App\Filament\Widgets\Speedtest\Insights;

use App\Enums\ResultStatus;
use App\Helpers\Number;
use App\Models\Result;
use Filament\Widgets\ChartWidget;

class AverageDownloadUploadChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Average Download & Upload per Month (Mbps)';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    public ?string $filter = 'month';

    protected function getPollingInterval(): ?string
    {
        return config('speedtest.dashboard_polling');
    }

    protected function getData(): array
    {
        // Fetch all results without additional filtering
        $results = Result::query()
            ->select(['download', 'upload', 'created_at'])
            ->where('status', '=', ResultStatus::Completed->value) // Filter by completed status
            ->orderBy('created_at')
            ->get();

        // Group results by month and calculate average download and upload
        $monthlyData = $results->groupBy(function ($item) {
            return $item->created_at->format('Y-m'); // Group by month (YYYY-MM)
        })->map(function ($items) {
            // Calculate the average download and upload speed for each month
            $averageDownload = $items->avg('download');
            $averageUpload = $items->avg('upload');

            return [
                'download' => Number::bitsToMagnitude(bits: $averageDownload * 8, precision: 2, magnitude: 'mbit'), // Adjust if needed
                'upload' => Number::bitsToMagnitude(bits: $averageUpload * 8, precision: 2, magnitude: 'mbit'), // Adjust if needed
            ];
        });

        // Convert month-year format to month names
        $labels = $monthlyData->keys()->map(function ($monthYear) {
            return \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->format('F Y'); // Convert YYYY-MM to "Month Year"
        })->toArray();

        $downloadData = $monthlyData->pluck('download')->toArray(); // Average download speeds
        $uploadData = $monthlyData->pluck('upload')->toArray(); // Average upload speeds

        return [
            'datasets' => [
                [
                    'label' => 'Average Download (Mbps)',
                    'data' => $downloadData,
                    'borderColor' => 'rgba(14, 165, 233)',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'pointBackgroundColor' => 'rgba(14, 165, 233)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Average Upload (Mbps)',
                    'data' => $uploadData,
                    'borderColor' => 'rgba(139, 92, 246)',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'pointBackgroundColor' => 'rgba(139, 92, 246)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true, // Enable tooltips
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
