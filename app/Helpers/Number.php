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
     */
    public static function bitsToMagnitude(int|float $bits, int $precision = 0, string $magnitude = 'kbit'): float
    {
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
    public static function toBitRate(int|float $bits, int $precision = 0, ?int $maxPrecision = null): string
    {
        $units = ['Bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps', 'Pbps', 'Ebps', 'Zbps', 'Ybps'];

        if ($bits === 0) {
            return '0 B';
        }

        for ($i = 0; ($bits / 1000) > 0.99 && ($i < count($units) - 1); $i++) {
            $bits /= 1000;
        }

        return sprintf('%s %s', static::format($bits, $precision, $maxPrecision), $units[$i]);
    }

    /**
     * Convert a file size string (e.g., "100 MB", "1 TB") to bytes.
     */
    public static function fileSizeToBytes(string $fileSize): int
    {
        $fileSize = trim($fileSize);

        // Extract the numeric value and unit
        if (!preg_match('/^(\d+(?:\.\d+)?)\s*([KMGTPEZY]?B)$/i', $fileSize, $matches)) {
            throw new \InvalidArgumentException("Invalid file size format: {$fileSize}");
        }

        $value = (float) $matches[1];
        $unit = strtoupper($matches[2]);

        $multipliers = [
            'B' => 1,
            'KB' => 1000,
            'MB' => 1000 ** 2,
            'GB' => 1000 ** 3,
            'TB' => 1000 ** 4,
            'PB' => 1000 ** 5,
            'EB' => 1000 ** 6,
            'ZB' => 1000 ** 7,
            'YB' => 1000 ** 8,
        ];

        if (!isset($multipliers[$unit])) {
            throw new \InvalidArgumentException("Unsupported unit: {$unit}");
        }

        return (int) ($value * $multipliers[$unit]);
    }
}
