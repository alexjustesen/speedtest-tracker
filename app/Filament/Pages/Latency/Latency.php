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
        $urls = LatencyResult::distinct()->pluck('url');

        return [
            'urls' => $urls,
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
        $urls = $this->getData()['urls'];

        $widgets = [];
        foreach ($urls as $url) {
            $widget = RecentLatencyChartWidget::make(['url' => $url]); // Pass URL during creation
            $widgets[] = $widget;
        }

        return $widgets;
    }
}
