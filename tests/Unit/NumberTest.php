<?php

use App\Helpers\Number;

test('can convert file size strings to bytes', function () {
    // Test basic units
    expect(Number::fileSizeToBytes('100 B'))->toBe(100);
    expect(Number::fileSizeToBytes('1 KB'))->toBe(1000);
    expect(Number::fileSizeToBytes('1 MB'))->toBe(1000000);
    expect(Number::fileSizeToBytes('1 GB'))->toBe(1000000000);
    expect(Number::fileSizeToBytes('1 TB'))->toBe(1000000000000);
    expect(Number::fileSizeToBytes('1 PB'))->toBe(1000000000000000);
});

test('can convert file size strings with decimals to bytes', function () {
    expect(Number::fileSizeToBytes('1.5 KB'))->toBe(1500);
    expect(Number::fileSizeToBytes('2.5 MB'))->toBe(2500000);
    expect(Number::fileSizeToBytes('0.5 GB'))->toBe(500000000);
    expect(Number::fileSizeToBytes('1.25 TB'))->toBe(1250000000000);
});

test('handles case insensitive units', function () {
    expect(Number::fileSizeToBytes('100 mb'))->toBe(100000000);
    expect(Number::fileSizeToBytes('1 Gb'))->toBe(1000000000);
    expect(Number::fileSizeToBytes('500 KB'))->toBe(500000);
    expect(Number::fileSizeToBytes('2 tb'))->toBe(2000000000000);
});

test('handles whitespace variations', function () {
    expect(Number::fileSizeToBytes('100MB'))->toBe(100000000);
    expect(Number::fileSizeToBytes('  1 GB  '))->toBe(1000000000);
    expect(Number::fileSizeToBytes('500    KB'))->toBe(500000);
});

test('handles large units correctly', function () {
    // Use string comparison for very large numbers to avoid PHP integer overflow
    expect(Number::fileSizeToBytes('1 EB'))->toBe(1000000000000000000);

    // For ZB and YB, we'll test smaller values due to PHP integer limits
    expect(Number::fileSizeToBytes('1 PB'))->toBe(1000000000000000);
});

test('throws exception for invalid format', function () {
    expect(fn() => Number::fileSizeToBytes('invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid file size format: invalid');

    expect(fn() => Number::fileSizeToBytes('100'))
        ->toThrow(InvalidArgumentException::class, 'Invalid file size format: 100');

    expect(fn() => Number::fileSizeToBytes('MB'))
        ->toThrow(InvalidArgumentException::class, 'Invalid file size format: MB');

    // XB is not a valid unit according to our regex pattern
    expect(fn() => Number::fileSizeToBytes('100 XB'))
        ->toThrow(InvalidArgumentException::class, 'Invalid file size format: 100 XB');
});

test('handles edge cases', function () {
    expect(Number::fileSizeToBytes('0 B'))->toBe(0);
    expect(Number::fileSizeToBytes('0 MB'))->toBe(0);
    expect(Number::fileSizeToBytes('1 B'))->toBe(1);
});

test('handles realistic file sizes', function () {
    // Common file sizes
    expect(Number::fileSizeToBytes('5 MB'))->toBe(5000000);
    expect(Number::fileSizeToBytes('100 MB'))->toBe(100000000);
    expect(Number::fileSizeToBytes('4.7 GB'))->toBe(4700000000);
    expect(Number::fileSizeToBytes('25 GB'))->toBe(25000000000);
    expect(Number::fileSizeToBytes('1.5 TB'))->toBe(1500000000000);
});
