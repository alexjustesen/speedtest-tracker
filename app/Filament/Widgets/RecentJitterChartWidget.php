<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Models\Result;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class RecentJitterChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Jitter';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    protected function getPollingInterval(): ?string
    {
        return config('speedtest.dashboard_polling');
    }

    protected function getData(): array
    {

        $startDate = $this->filters['startDate'] ?? now()->subWeek();
        $endDate = $this->filters['endDate'] ?? now();

        // Convert dates to the correct timezone if necessary
        $startDate = \Carbon\Carbon::parse($startDate)->startOfDay()->timezone(config('app.timezone'));
        $endDate = \Carbon\Carbon::parse($endDate)->endOfDay()->timezone(config('app.timezone'));

        $results = Result::query()
            ->select(['id', 'data', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->orderBy('created_at')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Download (ms)',
                    'data' => $downloadData = $results->map(fn ($item) => $item->download_jitter ? number_format($item->download_jitter, 2) : 0),
                    'borderColor' => 'rgba(14, 165, 233)',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'pointBackgroundColor' => 'rgba(14, 165, 233)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => $downloadData->count() <= 5 ? 3 : 0,
                ],
                [
                    'label' => 'Upload (ms)',
                    'data' => $uploadData = $results->map(fn ($item) => $item->upload_jitter ? number_format($item->upload_jitter, 2) : 0),
                    'borderColor' => 'rgba(139, 92, 246)',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'pointBackgroundColor' => 'rgba(139, 92, 246)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => $uploadData->count() <= 5 ? 3 : 0,
                ],
                [
                    'label' => 'Ping (ms)',
                    'data' => $pingData = $results->map(fn ($item) => $item->ping_jitter ? number_format($item->ping_jitter, 2) : 0),
                    'borderColor' => 'rgba(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'pointBackgroundColor' => 'rgba(16, 185, 129)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => $pingData->count() <= 5 ? 3 : 0,
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
