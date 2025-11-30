<?php

use App\Rules\FileSizeString;
use Illuminate\Support\Facades\Validator;

test('validates correct file size strings', function (string $size) {
    $validator = Validator::make(
        ['size' => $size],
        ['size' => new FileSizeString]
    );

    expect($validator->passes())->toBeTrue();
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

test('rejects invalid file size strings', function (string|int $size) {
    $validator = Validator::make(
        ['size' => $size],
        ['size' => new FileSizeString]
    );

    expect($validator->fails())->toBeTrue();
})->with([
    'invalid',
    '100',
    'MB',
    '100 XB',
    '100 ZB',
    'abc MB',
    '',
    ' ',
    123,
]);

test('provides meaningful error message', function () {
    $validator = Validator::make(
        ['file_size' => 'invalid'],
        ['file_size' => new FileSizeString]
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('file_size'))->toContain('valid file size');
});
