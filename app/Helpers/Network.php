<?php

namespace App\Helpers;

class Network
{
    /**
     * Check if the given ip is in a network range.
     */
    public static function ipInRange(string $ip, string $range): bool
    {
        [$range, $mask] = explode('/', $range) + [1 => '32'];

        $rangeDecimal = ip2long($range);

        $ipDecimal = ip2long($ip);

        $maskDecimal = ~((1 << (32 - (int) $mask)) - 1);

        return ($rangeDecimal & $maskDecimal) === ($ipDecimal & $maskDecimal);
    }
}
