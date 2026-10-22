<?php

namespace App\Helpers;

use Illuminate\Support\Number as SupportNumber;

class Number extends SupportNumber
{
    /**
     * Cast the given value to the type specified.
     */
    public static function castToType(mixed $value, string $type): mixed
    {
        if (is_null($value)) {
            return null;
        }

        settype($value, $type);

        return $value;
    }

    /**
     * Convert the given number to a specific bit order of magnitude.
     *
     * Returns null for a metric that was not measured, e.g. the upload of a file download test.
     */
    public static function bitsToMagnitude(null|int|float $bits, int $precision = 0, string $magnitude = 'kbit'): ?float
    {
        if (is_null($bits)) {
            return null;
        }

        $value = match ($magnitude) {
            'kbit' => $bits * 1000,
            'mbit' => $bits * pow(1000, -2),
            'gbit' => $bits * pow(1000, -3),
            'tbit' => $bits * pow(1000, -4),
            'pbit' => $bits * pow(1000, -5),
            'ebit' => $bits * pow(1000, -6),
            'zbit' => $bits * pow(1000, -7),
            'ybit' => $bits * pow(1000, -8),
            default => $bits,
        };

        return round(num: $value, precision: $precision);
    }

    /**
     * Convert the given number to its largest bit order of magnitude.
     *
     * Reference: https://en.wikipedia.org/wiki/Bit
     */
    public static function bitsToHuman(int|float $bits, int $precision = 0, ?int $maxPrecision = null): string
    {
        $units = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];

        if ($bits === 0) {
            return '0 B';
        }

        for ($i = 0; ($bits / 1000) > 0.99 && ($i < count($units) - 1); $i++) {
            $bits /= 1000;
        }

        return sprintf('%s %s', static::format($bits, $precision, $maxPrecision), $units[$i]);
    }

    /**
     * Convert the given number to its largest bit rate order of magnitude.
     *
     * Reference: https://en.wikipedia.org/wiki/Bit_rate
     */
    public static function toBitRate(null|int|float $bits, int $precision = 0, ?int $maxPrecision = null): string
    {
        // Not every service measures every metric, e.g. the file download has no upload.
        if (is_null($bits)) {
            return 'n/a';
        }

        $units = ['Bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps', 'Pbps', 'Ebps', 'Zbps', 'Ybps'];

        if ($bits === 0) {
            return '0 B';
        }

        for ($i = 0; ($bits / 1000) > 0.99 && ($i < count($units) - 1); $i++) {
            $bits /= 1000;
        }

        return sprintf('%s %s', static::format($bits, $precision, $maxPrecision), $units[$i]);
    }
}
