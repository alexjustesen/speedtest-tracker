<?php

namespace App\Helpers;

use Illuminate\Support\Number as SupportNumber;

class Number extends SupportNumber
{
    /**
     * Convert the given number to its largest bit order of magnitude.
     *
     * Reference: https://en.wikipedia.org/wiki/Bit
     */
    public static function bitsToHuman(int|float $bits, int $precision = 0, ?int $maxPrecision = null): string
    {
        $units = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];

        for ($i = 0; ($bits / 1000) > 0.9 && ($i < count($units) - 1); $i++) {
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

        for ($i = 0; ($bits / 1000) > 0.9 && ($i < count($units) - 1); $i++) {
            $bits /= 1000;
        }

        return sprintf('%s %s', static::format($bits, $precision, $maxPrecision), $units[$i]);
    }
}
