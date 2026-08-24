<?php

use App\Filament\Widgets\RecentDownloadLatencyChartWidget;
use App\Filament\Widgets\RecentUploadLatencyChartWidget;
use App\Models\Result;
use Livewire\Livewire;

function latencyChartDatasetLabels(string $widgetClass): array
{
    $component = Livewire::test($widgetClass)->instance();

    $method = new ReflectionMethod($component, 'getData');
    $method->setAccessible(true);

    return collect($method->invoke($component)['datasets'])->pluck('label')->all();
}

it('shows average, high, and low for download latency by default', function () {
    Result::factory()->create();

    expect(latencyChartDatasetLabels(RecentDownloadLatencyChartWidget::class))
        ->toEqual(['Average (ms)', 'High (ms)', 'Low (ms)']);
});

it('shows only average for download latency when chart_only_show_avg_latency is enabled', function () {
    config(['speedtest.chart_only_show_avg_latency' => true]);
    Result::factory()->create();

    expect(latencyChartDatasetLabels(RecentDownloadLatencyChartWidget::class))
        ->toEqual(['Average (ms)']);
});

it('shows average, high, and low for upload latency by default', function () {
    Result::factory()->create();

    expect(latencyChartDatasetLabels(RecentUploadLatencyChartWidget::class))
        ->toEqual(['Average (ms)', 'High (ms)', 'Low (ms)']);
});

it('shows only average for upload latency when chart_only_show_avg_latency is enabled', function () {
    config(['speedtest.chart_only_show_avg_latency' => true]);
    Result::factory()->create();

    expect(latencyChartDatasetLabels(RecentUploadLatencyChartWidget::class))
        ->toEqual(['Average (ms)']);
});
