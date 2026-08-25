<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Filament\Widgets\Concerns\HasChartFilters;
use App\Helpers\Average;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Widgets\ChartWidget;

class RecentPingChartWidget extends ChartWidget
{
    use HasChartFilters;

    protected ?string $heading = null;

    public function getHeading(): ?string
    {
        return __('general.ping_ms');
    }

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->resolveDateRange();

        $results = Result::query()
            ->select(['id', 'ping', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('general.ping'),
                    'data' => $results->map(fn ($item) => $item->ping),
                    'borderColor' => 'rgba(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'pointBackgroundColor' => 'rgba(16, 185, 129)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
                [
                    'label' => __('general.average'),
                    'data' => array_fill(0, count($results), Average::averagePing($results)),
                    'borderColor' => 'rgb(243, 7, 6, 1)',
                    'pointBackgroundColor' => 'rgb(243, 7, 6, 1)',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => 0,
                ],
            ],
            'labels' => $results->map(fn ($item) => $item->created_at->timezone(config('app.display_timezone'))->format(app(GeneralSettings::class)->chart_datetime_format)),
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
                    'beginAtZero' => app(GeneralSettings::class)->chart_begin_at_zero,
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
