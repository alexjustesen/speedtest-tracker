<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

class Benchmark
{
    /**
     * Validate if the bitrate passes the benchmark.
     */
    public static function bitrate(float|int $bytes, array $benchmark): bool
    {
        $value = Arr::get($benchmark, 'value');

        $unit = Arr::get($benchmark, 'unit');

        // Pass the benchmark if the value or unit is empty.
        if (blank($value) || blank($unit)) {
            return true;
        }

        return Bitrate::bytesToBits($bytes) < Bitrate::normalizeToBits($value.$unit);
    }

    /**
     * Validate if the ping passes the benchmark.
     */
    public static function ping(float|int $ping, array $benchmark): bool
    {
        $value = Arr::get($benchmark, 'value');

        // Pass the benchmark if the value is empty.
        if (blank($value)) {
            return true;
        }

        return $ping >= $value;
    }
}
