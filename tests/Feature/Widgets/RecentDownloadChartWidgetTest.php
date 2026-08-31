<?php

use App\Enums\ResultStatus;
use App\Filament\Widgets\RecentDownloadChartWidget;
use App\Filament\Widgets\RecentDownloadLatencyChartWidget;
use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Filament\Widgets\RecentUploadLatencyChartWidget;
use App\Models\Result;
use App\Models\User;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create());
    Carbon::setTestNow(Carbon::parse('2026-07-23 12:00:00'));
    app(GeneralSettings::class)->fill(['chart_default_range' => 1])->save();
});

afterEach(function () {
    Carbon::setTestNow();
});

it('only includes results within the applied custom date range', function () {
    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 100_000_000,
        'created_at' => now()->subHours(2),
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 50_000_000,
        'created_at' => now()->subDays(10),
    ]);

    $widget = Livewire::test(RecentDownloadChartWidget::class)
        ->dispatch('date-range-updated', [
            'dateFrom' => now()->subHours(3)->toDateTimeString(),
            'dateTo' => now()->toDateTimeString(),
        ])
        ->assertOk();

    $data = invade($widget->instance())->getData();

    expect($data['datasets'][0]['data'])->toHaveCount(1);
});

it('falls back to the admin-configured default window when no filter is applied', function () {
    app(GeneralSettings::class)->fill(['chart_default_range' => 7])->save();

    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 100_000_000,
        'created_at' => now()->subDays(3),
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 50_000_000,
        'created_at' => now()->subMonths(2),
    ]);

    $widget = Livewire::test(RecentDownloadChartWidget::class)
        ->assertOk();

    $data = invade($widget->instance())->getData();

    expect($data['datasets'][0]['data'])->toHaveCount(1);
});

it('mounts without error', function (string $widgetClass) {
    Livewire::test($widgetClass)->assertOk();
})->with([
    RecentDownloadChartWidget::class,
    RecentUploadChartWidget::class,
    RecentPingChartWidget::class,
    RecentJitterChartWidget::class,
    RecentDownloadLatencyChartWidget::class,
    RecentUploadLatencyChartWidget::class,
]);
