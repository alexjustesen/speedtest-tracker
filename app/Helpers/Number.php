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

    /**
     * Compare the dividend and divisor to get the percent change.
     */
    public static function percentChange(float $dividend, float $divisor, int $precision = 0): string
    {
        $quotient = ($dividend - $divisor) / $divisor;

        // TODO: determine if I want to return the full percentage (with %) or without.
        // return SupportNumber::percentage(($quotient * 100), precision: $precision);
        return number_format(round($quotient, $precision), $precision);
    }
}
