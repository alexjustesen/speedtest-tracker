<?php

namespace App\Helpers;

use InvalidArgumentException;

class Bitrate
{
    /**
     * Units conversion map to bits
     * Base unit is bits (not bytes)
     */
    private const UNITS = [
        'b' => 1,
        'kb' => 1000,
        'kib' => 1024,
        'mb' => 1000000,
        'mib' => 1048576,
        'gb' => 1000000000,
        'gib' => 1073741824,
        'tb' => 1000000000000,
        'tib' => 1099511627776,
    ];

    /**
     * Convert bytes to bits.
     */
    public static function bytesToBits(int|float $bytes): int|float
    {
        if ($bytes < 0) {
            throw new InvalidArgumentException('Bytes value cannot be negative');
        }

        // 1 byte = 8 bits
        return round($bytes * 8);
    }

    /**
     * Parse and normalize any bit rate to bits.
     */
    public static function normalizeToBits(float|int|string $bitrate): float
    {
        // If numeric, assume it's already in bits
        if (is_numeric($bitrate)) {
            return (float) $bitrate;
        }

        // Convert to lowercase and remove any whitespace
        $bitrate = strtolower(trim($bitrate));

        // Remove 'ps' or 'per second' suffix if present
        $bitrate = str_replace(['ps', 'per second'], '', $bitrate);

        // Extract numeric value and unit
        if (! preg_match('/^([\d.]+)\s*([kmgt]?i?b)$/', $bitrate, $matches)) {
            throw new InvalidArgumentException(
                "Invalid bitrate format. Expected format: '1.5 Mb', '500kb', etc."
            );
        }

        $value = (float) $matches[1];
        $unit = $matches[2];

        // Validate unit
        if (! isset(self::UNITS[$unit])) {
            throw new InvalidArgumentException(
                "Invalid unit '$unit'. Supported units: ".implode(', ', array_keys(self::UNITS))
            );
        }

        // Convert to bits
        return $value * self::UNITS[$unit];
    }

    /**
     * Format bits to human readable string.
     */
    public static function formatBits(float $bits, bool $useBinaryPrefix = false, int $precision = 2): string
    {
        $units = $useBinaryPrefix
            ? ['b', 'Kib', 'Mib', 'Gib', 'Tib']
            : ['b', 'kb', 'Mb', 'Gb', 'Tb'];

        $divisor = $useBinaryPrefix ? 1024 : 1000;
        $power = floor(($bits ? log($bits) : 0) / log($divisor));
        $power = min($power, count($units) - 1);

        return sprintf(
            "%.{$precision}f %s",
            $bits / pow($divisor, $power),
            $units[$power]
        );
    }
}
