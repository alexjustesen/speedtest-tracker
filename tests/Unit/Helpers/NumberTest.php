<?php

use App\Helpers\Number;

test('a metric that was not measured is formatted as not available', function () {
    expect(Number::toBitRate(bits: null, precision: 2))->toBe('n/a')
        ->and(Number::bitsToMagnitude(bits: null, magnitude: 'mbit'))->toBeNull()
        ->and(absoluteUploadThresholdFailed(50, null))->toBeFalse();
});

test('measured metrics are still formatted', function () {
    expect(Number::toBitRate(bits: 100_000_000, precision: 2))->toBe('100.00 Mbps')
        ->and(Number::bitsToMagnitude(bits: 100_000_000, magnitude: 'mbit'))->toEqual(100);
});
