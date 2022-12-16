<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Widgets\LineChartWidget;

class RecentPingChart extends LineChartWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getHeading(): string
    {
        return 'Ping';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
        ];
    }

    protected function getData(): array
    {
        $range = [];

        $settings = new GeneralSettings();

        switch ($this->filter) {
            case 'today':
                $range = [
                    ['created_at', '>=', now()->startOfDay()],
                    ['created_at', '<=', now()],
                ];
                break;

            case 'week':
                $range = [
                    ['created_at', '>=', now()->subWeek()],
                    ['created_at', '<=', now()],
                ];
                break;

            case 'month':
                $range = [
                    ['created_at', '>=', now()->subMonth()],
                    ['created_at', '<=', now()],
                ];
                break;
        }

        $results = Result::query()
            ->select(['ping', 'created_at'])
            ->where($range)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Ping (ms)',
                    'data' => $results->map(fn ($item) => $item->ping),
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => '#0ea5e9',
                ],
            ],
            'labels' => $results->map(fn ($item) => $item->created_at->timezone($settings->timezone)->format('M d - G:i')),
        ];
    }

    protected static ?array $options = [
        'plugins' => [
            //
        ],
        'scales' => [
            'y' => [
                'min' => 0,
            ],
        ],
    ];
}
