<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Filament\Widgets\Concerns\HasChartFilters;
use App\Helpers\Average;
use App\Helpers\Number;
use App\Models\Result;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Collection;

class RecentDownloadChartWidget extends ChartWidget
{
    use HasChartFilters;

    protected ?string $heading = null;

    public function getHeading(): ?string
    {
        return __('general.download_mbps');
    }

    public function getDescription(): ?string
    {
        $results = $this->getResults();

        return __('general.average').': '.number_format(Average::averageDownload($results), 2).' '.__('general.mbps');
    }

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected ?string $pollingInterval = '60s';

    protected function getResults(): Collection
    {
        [$startDate, $endDate] = $this->resolveDateRange();

        return Result::query()
            ->select(['id', 'download', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();
    }

    protected function getData(): array
    {
        $results = $this->getResults();

        return [
            'datasets' => [
                [
                    'label' => __('general.download'),
                    'data' => $results->map(fn ($item) => ! blank($item->download) ? Number::bitsToMagnitude(bits: $item->download_bits, precision: 2, magnitude: 'mbit') : null),
                    'borderColor' => 'rgba(14, 165, 233)',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'pointBackgroundColor' => 'rgba(14, 165, 233)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
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
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                    'position' => 'nearest',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => config('app.chart_begin_at_zero'),
                    'grace' => 2,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
