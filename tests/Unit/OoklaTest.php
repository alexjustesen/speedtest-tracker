<?php

use App\Helpers\Ookla;

test('negative metric values are clamped to zero', function () {
    $output = [
        'ping' => ['latency' => -3.2, 'jitter' => 1.1],
        'download' => ['bandwidth' => -12345, 'bytes' => -67890],
        'upload' => ['bandwidth' => 23456, 'bytes' => 78901],
    ];

    $clamped = Ookla::clampNegativeValues($output);

    expect($clamped['ping']['latency'])->toBe(0)
        ->and($clamped['download']['bandwidth'])->toBe(0)
        ->and($clamped['download']['bytes'])->toBe(0)
        ->and($clamped['upload']['bandwidth'])->toBe(23456)
        ->and($clamped['upload']['bytes'])->toBe(78901)
        ->and($clamped['ping']['jitter'])->toBe(1.1);
});

test('null and missing values pass through untouched', function () {
    $output = ['download' => ['bandwidth' => null]];

    $clamped = Ookla::clampNegativeValues($output);

    expect($clamped['download']['bandwidth'])->toBeNull()
        ->and($clamped)->not->toHaveKey('upload')
        ->and(Ookla::clampNegativeValues(null))->toBeNull();
});
