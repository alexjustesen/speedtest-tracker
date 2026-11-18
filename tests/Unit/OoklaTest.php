<?php

use App\Helpers\Ookla;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

function failedProcessException(string $stdout, string $stderr): ProcessFailedException
{
    $process = new Process(['sh', '-c', 'printf "%s" "$OUT"; printf "%s" "$ERR" >&2; exit 1'], env: [
        'OUT' => $stdout,
        'ERR' => $stderr,
    ]);

    $process->run();

    return new ProcessFailedException($process);
}

test('json error messages are extracted', function () {
    $exception = failedProcessException(
        '{"type":"log","timestamp":"2026-11-02T02:24:05Z","message":"Configuration - Could not retrieve or read configuration (ConfigurationError)","level":"error"}'.PHP_EOL,
        '',
    );

    expect(Ookla::getErrorMessage($exception))
        ->toBe('Configuration - Could not retrieve or read configuration (ConfigurationError)');
});

test('plain text rate limit error message is extracted', function () {
    $exception = failedProcessException('', "[2026-11-02 02:24:05.521] [error] Limit reached:\n\nToo many requests received. You may run the Speedtest CLI on a single computer for personal, non-commercial use only. For commercial licensing, visit https://www.ookla.com/speedtest-embedded.\n");

    expect(Ookla::getErrorMessage($exception))
        ->toBe('Limit reached: Too many requests received.');
});

test('unknown errors fall back to the generic message', function () {
    $exception = failedProcessException('', 'something went wrong');

    expect(Ookla::getErrorMessage($exception))
        ->toBe('An unexpected error occurred while running the Ookla CLI.');
});

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
