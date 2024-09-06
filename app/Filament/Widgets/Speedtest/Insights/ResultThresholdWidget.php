<?php

namespace App\Filament\Widgets\Speedtest\Insights;

use App\Models\Result;
use Filament\Widgets\ChartWidget;

class ResultThresholdWidget extends ChartWidget
{
    protected static ?string $heading = 'Threshold Statuses';

    protected int|string|array $columnSpan = 'half';

    protected static ?string $maxHeight = '250px';

    public ?string $filter = '24h';

    // Define a new property for threshold filtering
    public ?string $thresholdStatus = null;

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
        // Aggregate the count of each threshold status within the selected time frame
        $results = Result::query()
            ->select(['threshold_breached', \DB::raw('COUNT(*) as count')])
            ->when($this->filter == '24h', function ($query) {
                $query->where('created_at', '>=', now()->subDay());
            })
            ->when($this->filter == 'week', function ($query) {
                $query->where('created_at', '>=', now()->subWeek());
            })
            ->when($this->filter == 'month', function ($query) {
                $query->where('created_at', '>=', now()->subMonth());
            })
            // Apply threshold status if it is set
            ->when($this->thresholdStatus, function ($query) {
                $query->where('threshold_breached', $this->thresholdStatus);
            })
            ->groupBy('threshold_breached')
            ->get();

        // Define colors for each threshold status
        $statusColors = [
            'NotChecked' => '#ff9800', // Amber for not checked
            'Passed' => '#4caf50',       // Green for pass
            'Failed' => '#f44336',     // Red for failed
        ];

        // Prepare data for the pie chart
        $labels = $results->map(fn ($item) => $item->threshold_breached)->toArray();
        $data = $results->map(fn ($item) => $item->count)->toArray();
        $colors = $results->map(fn ($item) => $statusColors[$item->threshold_breached] ?? '#000000')->toArray(); // Default color if status not found

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors, // Set colors for each threshold status
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
                    'display' => true, // Show the legend
                    'position' => 'bottom', // Position of the legend
                    'labels' => [
                        'font' => [
                            'size' => 14, // Font size of legend labels
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true, // Show tooltips
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
