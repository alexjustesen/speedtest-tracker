<?php

use App\Models\Result;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('can use factory to create a Result model', function () {
    Result::factory()->create();

    expect(Result::count())->toBe(1);
});
