<?php

use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkType;
use App\Enums\UserRole;
use App\Filament\Resources\Benchmarks\BenchmarkResource;
use App\Filament\Resources\Benchmarks\Pages\ListBenchmarks;
use App\Models\Benchmark;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);

    // The migration seeds one fixed row per metric; start each test clean.
    Benchmark::query()->delete();
});

it('lists benchmarks for an admin', function () {
    $benchmarks = Benchmark::factory()->count(4)->create();

    actingAs($this->admin);

    Livewire::test(ListBenchmarks::class)
        ->assertOk()
        ->assertCanSeeTableRecords($benchmarks);
});

it('edits a benchmark through the table edit action', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'type' => BenchmarkType::Absolute,
        'absolute_value' => 50,
    ]);

    actingAs($this->admin);

    Livewire::test(ListBenchmarks::class)
        ->callAction(TestAction::make('edit')->table($benchmark), [
            'enabled' => true,
            'type' => BenchmarkType::Absolute->value,
            'absolute_value' => 150,
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas('benchmarks', [
        'id' => $benchmark->id,
        'absolute_value' => 150,
    ]);
});

it('does not allow enabling the toggle until a value is configured', function () {
    $benchmark = Benchmark::factory()->disabled()->create([
        'metric' => BenchmarkMetric::Download,
        'type' => BenchmarkType::Absolute,
        'absolute_value' => null,
    ]);

    actingAs($this->admin);

    Livewire::test(ListBenchmarks::class)
        ->call('updateTableColumnState', 'enabled', $benchmark->getKey(), true);

    expect($benchmark->refresh()->enabled)->toBeFalse();
});

it('allows toggling enabled once a value is configured', function () {
    $benchmark = Benchmark::factory()->disabled()->create([
        'metric' => BenchmarkMetric::Download,
        'type' => BenchmarkType::Absolute,
        'absolute_value' => 50,
    ]);

    actingAs($this->admin);

    Livewire::test(ListBenchmarks::class)
        ->call('updateTableColumnState', 'enabled', $benchmark->getKey(), true);

    expect($benchmark->refresh()->enabled)->toBeTrue();
});

it('has no create action', function () {
    actingAs($this->admin);

    Livewire::test(ListBenchmarks::class)
        ->assertActionDoesNotExist('create');
});

it('denies access to non-admin users', function () {
    actingAs(User::factory()->create(['role' => UserRole::User]));

    expect(BenchmarkResource::canAccess())->toBeFalse();
});
