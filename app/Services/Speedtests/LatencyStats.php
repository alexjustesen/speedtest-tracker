<?php

namespace App\Services\Speedtests;

class LatencyStats
{
    /**
     * Summarize latency samples in milliseconds the way the Ookla CLI reports them.
     */
    public static function summarize(array $samples): ?array
    {
        $samples = array_values(array_filter($samples, fn ($sample): bool => is_numeric($sample)));

        if (empty($samples)) {
            return null;
        }

        $sorted = $samples;
        sort($sorted);

        return [
            'latency' => round(self::median($sorted), 3),
            'jitter' => round(self::jitter($samples), 3),
            'low' => round($sorted[0], 3),
            'high' => round($sorted[count($sorted) - 1], 3),
            'iqm' => round(self::interquartileMean($sorted), 3),
        ];
    }

    private static function median(array $sorted): float
    {
        $middle = intdiv(count($sorted), 2);

        return count($sorted) % 2 === 0
            ? ($sorted[$middle - 1] + $sorted[$middle]) / 2
            : $sorted[$middle];
    }

    /**
     * Mean absolute difference between consecutive samples.
     */
    private static function jitter(array $samples): float
    {
        if (count($samples) < 2) {
            return 0;
        }

        $deltas = [];

        for ($i = 1; $i < count($samples); $i++) {
            $deltas[] = abs($samples[$i] - $samples[$i - 1]);
        }

        return array_sum($deltas) / count($deltas);
    }

    /**
     * Mean of the samples between the first and third quartile.
     */
    private static function interquartileMean(array $sorted): float
    {
        $count = count($sorted);
        $quarter = intdiv($count, 4);
        $middle = array_slice($sorted, $quarter, max($count - 2 * $quarter, 1));

        return array_sum($middle) / count($middle);
    }
}
