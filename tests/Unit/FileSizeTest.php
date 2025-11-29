<?php

use App\Helpers\FileSize;

test('converts bytes to bytes', function () {
    expect(FileSize::toBytes('100 B'))->toBe(100.0);
    expect(FileSize::toBytes('100B'))->toBe(100.0);
});

test('converts kilobytes to bytes', function () {
    expect(FileSize::toBytes('1 KB'))->toBe(1024.0);
    expect(FileSize::toBytes('1KB'))->toBe(1024.0);
    expect(FileSize::toBytes('100 KB'))->toBe(102400.0);
});

test('converts megabytes to bytes', function () {
    expect(FileSize::toBytes('1 MB'))->toBe(1048576.0);
    expect(FileSize::toBytes('1MB'))->toBe(1048576.0);
    expect(FileSize::toBytes('100 MB'))->toBe(104857600.0);
});

test('converts gigabytes to bytes', function () {
    expect(FileSize::toBytes('1 GB'))->toBe(1073741824.0);
    expect(FileSize::toBytes('1GB'))->toBe(1073741824.0);
    expect(FileSize::toBytes('2 GB'))->toBe(2147483648.0);
});

test('converts terabytes to bytes', function () {
    expect(FileSize::toBytes('1 TB'))->toBe(1099511627776.0);
    expect(FileSize::toBytes('1TB'))->toBe(1099511627776.0);
    expect(FileSize::toBytes('2 TB'))->toBe(2199023255552.0);
});

test('converts petabytes to bytes', function () {
    expect(FileSize::toBytes('1 PB'))->toBe(1125899906842624.0);
    expect(FileSize::toBytes('1PB'))->toBe(1125899906842624.0);
});

test('converts exabytes to bytes', function () {
    expect(FileSize::toBytes('1 EB'))->toBe(1152921504606846976.0);
    expect(FileSize::toBytes('1EB'))->toBe(1152921504606846976.0);
});

test('handles decimal values', function () {
    expect(FileSize::toBytes('1.5 MB'))->toBe(1572864.0);
    expect(FileSize::toBytes('2.5 GB'))->toBe(2684354560.0);
});

test('handles case insensitive units', function () {
    expect(FileSize::toBytes('100 mb'))->toBe(104857600.0);
    expect(FileSize::toBytes('1 gb'))->toBe(1073741824.0);
    expect(FileSize::toBytes('1 Mb'))->toBe(1048576.0);
});

test('handles extra whitespace', function () {
    expect(FileSize::toBytes('  100 MB  '))->toBe(104857600.0);
    expect(FileSize::toBytes('100  MB'))->toBe(104857600.0);
});

test('throws exception for invalid format', function () {
    FileSize::toBytes('invalid');
})->throws(InvalidArgumentException::class, 'Invalid file size format');

test('throws exception for invalid unit', function () {
    FileSize::toBytes('100 XB');
})->throws(InvalidArgumentException::class);

test('throws exception for missing value', function () {
    FileSize::toBytes('MB');
})->throws(InvalidArgumentException::class);

test('throws exception for missing unit', function () {
    FileSize::toBytes('100');
})->throws(InvalidArgumentException::class);

test('validates correct file size strings', function (string $size) {
    expect(FileSize::isValid($size))->toBeTrue();
})->with([
    '100 B',
    '100B',
    '1 KB',
    '1KB',
    '100 MB',
    '100MB',
    '1 GB',
    '1GB',
    '2 TB',
    '2TB',
    '1.5 MB',
    '2.5 GB',
    '100 mb',
    '1 gb',
]);

test('invalidates incorrect file size strings', function (string $size) {
    expect(FileSize::isValid($size))->toBeFalse();
})->with([
    'invalid',
    '100',
    'MB',
    '100 XB',
    '100 ZB',
    'abc MB',
    '',
    ' ',
]);

test('converts bytes to human readable format', function () {
    expect(FileSize::fromBytes(0))->toBe('0 B');
    expect(FileSize::fromBytes(100))->toBe('100 B');
    expect(FileSize::fromBytes(1024))->toBe('1 KB');
    expect(FileSize::fromBytes(1048576))->toBe('1 MB');
    expect(FileSize::fromBytes(1073741824))->toBe('1 GB');
    expect(FileSize::fromBytes(1099511627776))->toBe('1 TB');
});

test('converts bytes to human readable format with custom precision', function () {
    expect(FileSize::fromBytes(1572864, 1))->toBe('1.5 MB');
    expect(FileSize::fromBytes(2684354560, 2))->toBe('2.5 GB');
    expect(FileSize::fromBytes(1536, 0))->toBe('2 KB');
});
