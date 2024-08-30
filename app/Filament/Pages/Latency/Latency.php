<?php

namespace App\Filament\Pages\Latency;

use App\Filament\Widgets\Latency\RecentLatencyChartWidget;
use App\Models\LatencyResult;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Pages\Page;

class Latency extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';

    protected static string $view = 'filament.pages.latency-results-page';

    protected static ?string $navigationGroup = 'Latency';

    protected static ?string $navigationLabel = 'Overview';

    public function getSubheading(): ?string
    {
        if (blank(config('latency.schedule'))) {
            return __('No latency tests scheduled.');
        }

        $cronExpression = new CronExpression(config('latency.schedule'));

        $nextRunDate = Carbon::parse($cronExpression->getNextRunDate(timeZone: config('app.display_timezone')))->format(config('app.datetime_format'));

        return 'Next latency tests at: '.$nextRunDate;
    }

    public function getData()
    {
        // Retrieve distinct URLs
        $target_url = LatencyResult::distinct()->pluck('target_url');

        return [
            'target_url' => $target_url,
            'filters' => $this->getFilters(),
        ];
    }

    protected function getFilters(): array
    {
        return [
            '24h' => 'Last 24h',
            'week' => 'Last week',
            'month' => 'Last month',
        ];
    }

    protected function getHeaderWidgets(): array
    {
        $target_url = $this->getData()['target_url'];

        $widgets = [];
        foreach ($target_url as $target_url) {
            $widget = RecentLatencyChartWidget::make(['target_url' => $target_url]); // Pass URL during creation
            $widgets[] = $widget;
        }

        return $widgets;
    }
}
