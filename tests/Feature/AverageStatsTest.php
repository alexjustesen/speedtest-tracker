<?php

use App\Enums\ResultStatus;
use App\Livewire\AverageStats;
use App\Models\Result;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
});

describe('average stats component', function () {
    test('can render the component', function () {
        actingAs($this->user);

        Result::factory()->count(5)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subHours(2),
        ]);

        Livewire::test(AverageStats::class)
            ->assertStatus(200)
            ->assertSee(__('general.average'));
    });

    test('displays average download speed', function () {
        actingAs($this->user);

        Result::factory()->count(3)->create([
            'status' => ResultStatus::Completed,
            'download' => 100000000,
            'created_at' => now()->subHours(2),
        ]);

        Livewire::test(AverageStats::class)
            ->assertStatus(200)
            ->assertSee(__('general.download'));
    });

    test('displays average upload speed', function () {
        actingAs($this->user);

        Result::factory()->count(3)->create([
            'status' => ResultStatus::Completed,
            'upload' => 50000000,
            'created_at' => now()->subHours(2),
        ]);

        Livewire::test(AverageStats::class)
            ->assertStatus(200)
            ->assertSee(__('general.upload'));
    });

    test('displays average ping', function () {
        actingAs($this->user);

        Result::factory()->count(3)->create([
            'status' => ResultStatus::Completed,
            'ping' => 20.5,
            'created_at' => now()->subHours(2),
        ]);

        Livewire::test(AverageStats::class)
            ->assertStatus(200)
            ->assertSee(__('general.ping'));
    });

    test('filters results by 24h range', function () {
        actingAs($this->user);

        Result::factory()->count(3)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subHours(12),
        ]);

        Result::factory()->count(2)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subDays(2),
        ]);

        Livewire::test(AverageStats::class)
            ->set('filter', '24h')
            ->assertStatus(200)
            ->call('$refresh');
    });

    test('filters results by week range', function () {
        actingAs($this->user);

        Result::factory()->count(3)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subDays(3),
        ]);

        Result::factory()->count(2)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subDays(10),
        ]);

        Livewire::test(AverageStats::class)
            ->set('filter', 'week')
            ->assertStatus(200)
            ->call('$refresh');
    });

    test('filters results by month range', function () {
        actingAs($this->user);

        Result::factory()->count(3)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subDays(15),
        ]);

        Result::factory()->count(2)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subDays(60),
        ]);

        Livewire::test(AverageStats::class)
            ->set('filter', 'month')
            ->assertStatus(200)
            ->call('$refresh');
    });

    test('updates filter when filter button is clicked', function () {
        actingAs($this->user);

        Result::factory()->count(3)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subHours(12),
        ]);

        Livewire::test(AverageStats::class)
            ->assertSet('filter', config('speedtest.default_chart_range', '24h'))
            ->set('filter', 'week')
            ->assertSet('filter', 'week')
            ->set('filter', 'month')
            ->assertSet('filter', 'month');
    });

    test('only includes completed results', function () {
        actingAs($this->user);

        Result::factory()->count(3)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subHours(2),
        ]);

        Result::factory()->count(2)->create([
            'status' => ResultStatus::Failed,
            'created_at' => now()->subHours(2),
        ]);

        $component = Livewire::test(AverageStats::class)
            ->assertStatus(200);

        expect($component->results->count())->toBe(3);
    });

    test('does not render when no results exist', function () {
        actingAs($this->user);

        $response = $this->get('/admin');

        $response->assertStatus(200)
            ->assertDontSee(__('general.average'));
    });

    test('initializes with default filter from config', function () {
        actingAs($this->user);

        config()->set('speedtest.default_chart_range', 'week');

        Result::factory()->count(3)->create([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subHours(2),
        ]);

        Livewire::test(AverageStats::class)
            ->assertSet('filter', 'week');
    });
});
