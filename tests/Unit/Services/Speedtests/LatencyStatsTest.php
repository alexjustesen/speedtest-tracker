<?php

use App\Services\Speedtests\LatencyStats;

test('latency samples are summarized like the Ookla CLI reports them', function () {
    $stats = LatencyStats::summarize([10, 12, 11, 30, 9, 10, 11, 12]);

    expect($stats)->toBe([
        'latency' => 11.0,
        'jitter' => 6.571,
        'low' => 9.0,
        'high' => 30.0,
        'iqm' => 11.0,
    ]);
});

test('a single sample has no jitter', function () {
    expect(LatencyStats::summarize([42.5]))->toMatchArray([
        'latency' => 42.5,
        'jitter' => 0.0,
        'low' => 42.5,
        'high' => 42.5,
        'iqm' => 42.5,
    ]);
});

test('no samples summarize to null', function () {
    expect(LatencyStats::summarize([]))->toBeNull();
});
