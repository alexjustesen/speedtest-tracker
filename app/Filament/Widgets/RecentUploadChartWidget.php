<?php

namespace App\Filament\Widgets;

use App\Helpers\Average;
use App\Helpers\Number;

class RecentUploadChartWidget extends RecentChartWidget
{
    protected static ?string $heading = 'Upload (Mbps)';

    protected function getData(): array
    {
        $results = $this->getResults('upload');

        return [
            'datasets' => [
                [
                    'label' => 'Upload',
                    'data' => $results->map(fn ($item) => ! blank($item->upload) ? Number::bitsToMagnitude(bits: $item->upload_bits, precision: 2, magnitude: 'mbit') : null),
                    'borderColor' => 'rgba(139, 92, 246)',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'pointBackgroundColor' => 'rgba(139, 92, 246)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
                [
                    'label' => 'Average',
                    'data' => array_fill(0, count($results), Average::averageUpload($results)),
                    'borderColor' => 'rgb(243, 7, 6, 1)',
                    'pointBackgroundColor' => 'rgb(243, 7, 6, 1)',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => 0,
                ],
            ],
            'labels' => $results->map(fn ($item) => $item->created_at->timezone(config('app.display_timezone'))->format(config('app.chart_datetime_format'))),
        ];
    }
}
