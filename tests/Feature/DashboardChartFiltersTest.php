<?php

use App\Enums\ResultStatus;
use App\Enums\UserRole;
use App\Models\Result;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('renders the shared date range filter alongside every chart widget', function () {
    Result::factory()->create(['status' => ResultStatus::Completed]);

    actingAs(User::factory()->create(['role' => UserRole::Admin]));

    $response = get(route('home'));

    $response->assertOk();
    $response->assertSeeLivewire('date-range-filter');
    $response->assertSeeLivewire(\App\Filament\Widgets\RecentDownloadChartWidget::class);
    $response->assertSeeLivewire(\App\Filament\Widgets\RecentUploadChartWidget::class);
    $response->assertSeeLivewire(\App\Filament\Widgets\RecentPingChartWidget::class);
    $response->assertSeeLivewire(\App\Filament\Widgets\RecentJitterChartWidget::class);
    $response->assertSeeLivewire(\App\Filament\Widgets\RecentDownloadLatencyChartWidget::class);
    $response->assertSeeLivewire(\App\Filament\Widgets\RecentUploadLatencyChartWidget::class);
});
