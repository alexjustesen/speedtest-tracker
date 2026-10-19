<?php

namespace App\Filament\Widgets\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasTimeSeriesChart
{
    /**
     * Gaps longer than this many times the typical interval between results break the line.
     */
    protected int $gapIntervalMultiplier = 3;

    /**
     * Map the results to ISO 8601 timestamps for the time scale.
     *
     * @return array<int, string>
     */
    protected function timeLabels(Collection $results): array
    {
        return $results
            ->map(fn ($item) => $item->created_at->toIso8601String())
            ->values()
            ->all();
    }

    /**
     * The maximum gap in milliseconds the line is drawn across, derived from the median
     * interval between results so it adapts to any (or multiple) schedules.
     */
    protected function gapThreshold(Collection $results): int|bool
    {
        if ($results->count() < 3) {
            return true;
        }

        $intervals = $results
            ->values()
            ->sliding(2)
            ->map(fn ($pair) => $pair->first()->created_at->diffInSeconds($pair->last()->created_at, true));

        $median = max((int) $intervals->median(), 60);

        return $median * $this->gapIntervalMultiplier * 1000;
    }

    /**
     * @return array<string, mixed>
     */
    protected function timeScaleOptions(): array
    {
        $format = $this->toLuxonFormat(config('app.chart_datetime_format'));

        return [
            'type' => 'time',
            'adapters' => [
                'date' => [
                    'zone' => config('app.display_timezone'),
                    'locale' => Str::replace('_', '-', app()->getLocale()),
                ],
            ],
            'time' => [
                'tooltipFormat' => $format,
                'displayFormats' => collect(['millisecond', 'second', 'minute', 'hour', 'day', 'week', 'month', 'quarter', 'year'])
                    ->mapWithKeys(fn (string $unit) => [$unit => $format])
                    ->all(),
            ],
            'ticks' => [
                'maxRotation' => 0,
                'autoSkip' => true,
                'autoSkipPadding' => 20,
            ],
        ];
    }

    /**
     * Convert a PHP date format string to a Luxon format string.
     */
    protected function toLuxonFormat(string $format): string
    {
        $map = [
            'd' => 'dd', 'D' => 'ccc', 'j' => 'd', 'l' => 'cccc', 'N' => 'E',
            'F' => 'LLLL', 'M' => 'LLL', 'm' => 'LL', 'n' => 'L',
            'Y' => 'yyyy', 'y' => 'yy',
            'a' => 'a', 'A' => 'a',
            'g' => 'h', 'G' => 'H', 'h' => 'hh', 'H' => 'HH',
            'i' => 'mm', 's' => 'ss',
            'e' => 'z', 'T' => 'ZZZZ', 'P' => 'ZZ', 'O' => 'ZZZ',
        ];

        $result = '';
        $literal = '';

        $flushLiteral = function () use (&$result, &$literal) {
            if ($literal !== '') {
                $result .= "'".str_replace("'", "''", $literal)."'";
                $literal = '';
            }
        };

        $chars = mb_str_split($format);

        for ($i = 0; $i < count($chars); $i++) {
            $char = $chars[$i];

            if ($char === '\\' && isset($chars[$i + 1])) {
                $literal .= $chars[++$i];

                continue;
            }

            if (isset($map[$char])) {
                $flushLiteral();
                $result .= $map[$char];

                continue;
            }

            if (ctype_alpha($char)) {
                // Unsupported PHP token, keep it out of the output rather than misrender it.
                continue;
            }

            $literal .= $char;
        }

        $flushLiteral();

        return $result;
    }
}
