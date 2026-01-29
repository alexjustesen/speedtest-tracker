<?php

use App\Models\Result;
use App\Services\DataUsageCalculator;
use Carbon\Carbon;

it('calculates total data usage for results within the period', function () {
    $startDate = Carbon::parse('2024-01-01 00:00:00');
    $endDate = Carbon::parse('2024-01-31 23:59:59');

    // Create results within the period
    Result::factory()->create([
        'download_bytes' => 1000000,
        'upload_bytes' => 500000,
        'created_at' => Carbon::parse('2024-01-15 12:00:00'),
    ]);

    Result::factory()->create([
        'download_bytes' => 2000000,
        'upload_bytes' => 1000000,
        'created_at' => Carbon::parse('2024-01-20 15:30:00'),
    ]);

    $result = DataUsageCalculator::calculate($startDate, $endDate);

    expect($result)->toBeArray()
        ->and($result['download_bytes'])->toBe(3000000)
        ->and($result['upload_bytes'])->toBe(1500000)
        ->and($result['total_bytes'])->toBe(4500000);
});

it('returns zero when no results exist in the period', function () {
    $startDate = Carbon::parse('2024-01-01 00:00:00');
    $endDate = Carbon::parse('2024-01-31 23:59:59');

    // Create a result outside the period
    Result::factory()->create([
        'download_bytes' => 1000000,
        'upload_bytes' => 500000,
        'created_at' => Carbon::parse('2024-02-15 12:00:00'),
    ]);

    $result = DataUsageCalculator::calculate($startDate, $endDate);

    expect($result)->toBeArray()
        ->and($result['download_bytes'])->toBe(0)
        ->and($result['upload_bytes'])->toBe(0)
        ->and($result['total_bytes'])->toBe(0);
});

it('handles null byte values correctly', function () {
    $startDate = Carbon::parse('2024-01-01 00:00:00');
    $endDate = Carbon::parse('2024-01-31 23:59:59');

    // Create results with null bytes
    Result::factory()->create([
        'download_bytes' => null,
        'upload_bytes' => null,
        'created_at' => Carbon::parse('2024-01-15 12:00:00'),
    ]);

    Result::factory()->create([
        'download_bytes' => 1000000,
        'upload_bytes' => 500000,
        'created_at' => Carbon::parse('2024-01-20 15:30:00'),
    ]);

    $result = DataUsageCalculator::calculate($startDate, $endDate);

    expect($result)->toBeArray()
        ->and($result['download_bytes'])->toBe(1000000)
        ->and($result['upload_bytes'])->toBe(500000)
        ->and($result['total_bytes'])->toBe(1500000);
});

it('includes results at the exact start boundary', function () {
    $startDate = Carbon::parse('2024-01-01 00:00:00');
    $endDate = Carbon::parse('2024-01-31 23:59:59');

    Result::factory()->create([
        'download_bytes' => 1000000,
        'upload_bytes' => 500000,
        'created_at' => $startDate,
    ]);

    $result = DataUsageCalculator::calculate($startDate, $endDate);

    expect($result['total_bytes'])->toBe(1500000);
});

it('includes results at the exact end boundary', function () {
    $startDate = Carbon::parse('2024-01-01 00:00:00');
    $endDate = Carbon::parse('2024-01-31 23:59:59');

    Result::factory()->create([
        'download_bytes' => 1000000,
        'upload_bytes' => 500000,
        'created_at' => $endDate,
    ]);

    $result = DataUsageCalculator::calculate($startDate, $endDate);

    expect($result['total_bytes'])->toBe(1500000);
});

it('excludes results before the start date', function () {
    $startDate = Carbon::parse('2024-01-01 00:00:00');
    $endDate = Carbon::parse('2024-01-31 23:59:59');

    Result::factory()->create([
        'download_bytes' => 1000000,
        'upload_bytes' => 500000,
        'created_at' => Carbon::parse('2023-12-31 23:59:59'),
    ]);

    $result = DataUsageCalculator::calculate($startDate, $endDate);

    expect($result['total_bytes'])->toBe(0);
});

it('excludes results after the end date', function () {
    $startDate = Carbon::parse('2024-01-01 00:00:00');
    $endDate = Carbon::parse('2024-01-31 23:59:59');

    Result::factory()->create([
        'download_bytes' => 1000000,
        'upload_bytes' => 500000,
        'created_at' => Carbon::parse('2024-02-01 00:00:01'),
    ]);

    $result = DataUsageCalculator::calculate($startDate, $endDate);

    expect($result['total_bytes'])->toBe(0);
});
