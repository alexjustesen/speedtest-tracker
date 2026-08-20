<?php

use App\Models\Result;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create(), ['results:read']);
});

test('filter[start_at] returns only results on or after the start of that day', function () {
    $before = Result::factory()->create(['created_at' => '2025-12-31 23:00:00']);
    $onDay = Result::factory()->create(['created_at' => '2026-01-01 00:30:00']);
    $after = Result::factory()->create(['created_at' => '2026-01-02 10:00:00']);

    $response = $this->getJson('/api/v1/results?filter[start_at]=2026-01-01');

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($onDay->id, $after->id)
        ->not->toContain($before->id);
});

test('filter[end_at] with a date-only value includes results from the entire end day', function () {
    $sameDayAfternoon = Result::factory()->create(['created_at' => '2026-01-31 15:00:00']);
    $nextDay = Result::factory()->create(['created_at' => '2026-02-01 00:10:00']);

    $response = $this->getJson('/api/v1/results?filter[end_at]=2026-01-31');

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($sameDayAfternoon->id)
        ->not->toContain($nextDay->id);
});

test('combined start_at and end_at return the inclusive range', function () {
    $before = Result::factory()->create(['created_at' => '2025-12-31 12:00:00']);
    $inRangeStart = Result::factory()->create(['created_at' => '2026-01-01 00:00:00']);
    $inRangeEnd = Result::factory()->create(['created_at' => '2026-01-31 23:30:00']);
    $after = Result::factory()->create(['created_at' => '2026-02-01 08:00:00']);

    $response = $this->getJson('/api/v1/results?filter[start_at]=2026-01-01&filter[end_at]=2026-01-31');

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($inRangeStart->id, $inRangeEnd->id)
        ->not->toContain($before->id, $after->id);
});

test('filter[end_at] with an explicit time is honored literally', function () {
    $beforeCutoff = Result::factory()->create(['created_at' => '2026-01-31 11:00:00']);
    $afterCutoff = Result::factory()->create(['created_at' => '2026-01-31 15:00:00']);

    $response = $this->getJson('/api/v1/results?filter[end_at]=2026-01-31 12:00:00');

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($beforeCutoff->id)
        ->not->toContain($afterCutoff->id);
});

test('an invalid date filter returns a 422', function () {
    $response = $this->getJson('/api/v1/results?filter[start_at]=not-a-date');

    $response->assertStatus(422);
});
