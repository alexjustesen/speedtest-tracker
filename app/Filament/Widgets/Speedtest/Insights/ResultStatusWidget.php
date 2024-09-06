<?php

namespace App\Filament\Widgets\Speedtest\Insights;

use App\Enums\ResultStatus;
use App\Models\Result;
use Filament\Widgets\ChartWidget;

class ResultStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Result Statuses';

    protected int|string|array $columnSpan = 'half';

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
        // Aggregate the count of each status within the selected time frame
        $results = Result::query()
            ->select(['status', \DB::raw('COUNT(*) as count')])
            ->when($this->filter == '24h', function ($query) {
                $query->where('created_at', '>=', now()->subDay());
            })
            ->when($this->filter == 'week', function ($query) {
                $query->where('created_at', '>=', now()->subWeek());
            })
            ->when($this->filter == 'month', function ($query) {
                $query->where('created_at', '>=', now()->subMonth());
            })
            ->groupBy('status')
            ->get();

        // Define colors for each status using string keys
        $statusColors = [
            ResultStatus::Completed->value => '#4caf50', // Green for completed
            ResultStatus::Failed->value => '#f44336',    // Red for failed
            ResultStatus::Started->value => '#ff9800',   // Amber for started
        ];

        // Prepare data for the pie chart
        $labels = $results->map(fn ($item) => $item->status->value)->toArray(); // Ensure status is a string
        $data = $results->map(fn ($item) => $item->count)->toArray();
        $colors = $results->map(fn ($item) => $statusColors[$item->status->value] ?? '#000000')->toArray(); // Default color if status not found

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors, // Set colors for each status
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
