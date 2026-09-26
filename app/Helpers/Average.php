<?php

namespace App\Helpers;

use App\Enums\ResultStatus;
use Illuminate\Support\Collection;

class Average
{
    /**
     * Calculate the average download speed from a collection of results.
     */
    public static function averageDownload(Collection $results, int $precision = 2, string $magnitude = 'mbit'): float
    {
        return round(
            self::completed($results)->map(function ($item) use ($magnitude, $precision) {
                return ! blank($item->download)
                    ? Number::bitsToMagnitude(bits: $item->download_bits, precision: $precision, magnitude: $magnitude)
                    : 0;
            })->avg(),
            $precision
        );
    }

    public static function averageUpload(Collection $results, int $precision = 2, string $magnitude = 'mbit'): float
    {
        return round(
            self::completed($results)->map(function ($item) use ($magnitude, $precision) {
                return ! blank($item->upload)
                    ? Number::bitsToMagnitude(bits: $item->upload_bits, precision: $precision, magnitude: $magnitude)
                    : 0;
            })->avg(),
            $precision
        );
    }

    public static function averagePing(Collection $results, int $precision = 2): float
    {
        $avgPing = self::completed($results)->filter(function ($item) {
            return ! blank($item->ping);
        })->avg('ping');

        return round($avgPing, $precision);
    }

    /**
     * Only completed results count towards an average.
     */
    protected static function completed(Collection $results): Collection
    {
        return $results->where('status', ResultStatus::Completed);
    }
}
