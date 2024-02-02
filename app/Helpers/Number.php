<?php

namespace App\Helpers;

use Illuminate\Support\Number as SupportNumber;

class Number extends SupportNumber
{
    /**
     * Convert the given number to its file size equivalent in bits.
     */
    public static function fileSizeBits(int|float $bits, int $precision = 0, ?int $maxPrecision = null, bool $perSecond = false): string
    {
        $units = match ($perSecond) {
            true => ['Bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps', 'Pbps', 'Ebps', 'Zbps', 'Ybps'],
            default => ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb']
        };

        for ($i = 0; ($bits / 1024) > 0.9 && ($i < count($units) - 1); $i++) {
            $bits /= 1024;
        }

        return sprintf('%s %s', static::format($bits, $precision, $maxPrecision), $units[$i]);
    }
}
