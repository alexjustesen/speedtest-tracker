<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\Number;
use App\Helpers\TimeZoneHelper;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

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
        $settings = new GeneralSettings();

        $startDate = $this->filters['startDate'] ?? now()->subWeek();
        $endDate = $this->filters['endDate'] ?? now();

        $results = Result::query()
            ->select(['id', 'download', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->orderBy('created_at')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Download',
                    'data' => $results->map(fn ($item) => ! blank($item->download) ? Number::bitsToMagnitude(bits: $item->download_bits, precision: 2, magnitude: 'mbit') : 0),
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => '#0ea5e9',
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
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
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
