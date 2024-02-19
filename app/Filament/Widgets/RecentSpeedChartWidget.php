<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\TimeZoneHelper;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Widgets\ChartWidget;

class RecentSpeedChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Download / Upload';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

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
        $settings = new GeneralSettings();

        $results = Result::query()
            ->select(['id', 'download', 'upload', 'created_at'])
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
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Download',
                    'data' => $results->map(fn ($item) => ! blank($item->download) ? toBits(convertSize($item->download), 2) : 0),
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => '#0ea5e9',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Upload',
                    'data' => $results->map(fn ($item) => ! blank($item->upload) ? toBits(convertSize($item->upload), 2) : 0),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => '#8b5cf6',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $results->map(fn ($item) => $item->created_at->timezone(TimeZoneHelper::displayTimeZone($settings))->format('M d - G:i')),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                    ],
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Mbps',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
