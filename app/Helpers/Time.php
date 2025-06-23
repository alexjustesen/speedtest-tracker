<?php

namespace App\Helpers;

class Time
{
    public static function formatElapsed(int $milliseconds, int $precision = 2): string
    {
        if ($milliseconds < 1000) {
            return "{$milliseconds} ms";
        }

        $seconds = round($milliseconds / 1000, $precision);

        return "{$seconds} sec";
    }

    public static function totalElapsed(int $downloadMs, int $uploadMs, int $precision = 2): string
    {
        $total = $downloadMs + $uploadMs;

        return self::formatElapsed($total, $precision);
    }
}
