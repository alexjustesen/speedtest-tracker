<?php

namespace App\Filament\Pages\Latency;

use App\Filament\Widgets\Latency\RecentLatencyChartWidget;
use App\Models\LatencyResult;
use App\Settings\LatencySettings;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Pages\Page;

class Latency extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static string $view = 'filament.pages.latency-results-page';

    protected static ?string $navigationGroup = 'Latency';

    protected static ?string $navigationLabel = 'Overview';

    public function getSubheading(): ?string
    {
        $settings = app(LatencySettings::class);

        if (! $settings->latency_enabled || blank($settings->latency_schedule)) {
            return __('No latency tests scheduled.');
        }

        $cronExpression = new CronExpression($settings->latency_schedule);

        $nextRunDate = Carbon::parse($cronExpression->getNextRunDate())
            ->setTimezone(config('app.display_timezone'))
            ->format(config('app.datetime_format'));

        return 'Next latency test at: '.$nextRunDate;
    }

    public function getData()
    {
        // Retrieve distinct target names
        $target_names = LatencyResult::distinct()->pluck('target_name');

        return [
            'target_names' => $target_names,
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
        $target_names = $this->getData()['target_names'];

        $widgets = [];
        foreach ($target_names as $target_name) {
            $widget = RecentLatencyChartWidget::make(['target_name' => $target_name]); // Pass target_name during creation
            $widgets[] = $widget;
        }

        return $widgets;
    }
}
