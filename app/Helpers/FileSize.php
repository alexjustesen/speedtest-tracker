<?php

namespace App\Helpers;

class FileSize
{
    /**
     * Unit multipliers in bytes.
     */
    protected const array UNITS = [
        'B' => 1,
        'KB' => 1024,
        'MB' => 1048576,
        'GB' => 1073741824,
        'TB' => 1099511627776,
        'PB' => 1125899906842624,
        'EB' => 1152921504606846976,
    ];

    /**
     * Convert a file size string to bytes.
     *
     * @param  string  $size  File size string (e.g., "100 MB", "1GB", "2.5TB")
     * @return int|float The size in bytes
     *
     * @throws \InvalidArgumentException If the format is invalid
     */
    public static function toBytes(string $size): int|float
    {
        $size = trim($size);

        if (! preg_match('/^(\d+(?:\.\d+)?)\s*(B|KB|MB|GB|TB|PB|EB)$/i', $size, $matches)) {
            throw new \InvalidArgumentException("Invalid file size format: {$size}");
        }

        $value = (float) $matches[1];
        $unit = strtoupper($matches[2]);

        if (! isset(self::UNITS[$unit])) {
            throw new \InvalidArgumentException("Unknown file size unit: {$unit}");
        }

        return $value * self::UNITS[$unit];
    }

    /**
     * Check if a string is a valid file size format.
     */
    public static function isValid(string $size): bool
    {
        $size = trim($size);

        return (bool) preg_match('/^(\d+(?:\.\d+)?)\s*(B|KB|MB|GB|TB|PB|EB)$/i', $size);
    }
}
