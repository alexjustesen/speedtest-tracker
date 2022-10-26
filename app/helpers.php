<?php

use App\Models\Result;
use App\Settings\ThresholdSettings;

if (! function_exists('absoluteThresholdPassed')) {
    function absoluteThresholdPassed(Result $result): bool
    {
        $thresholds = new (ThresholdSettings::class);

        if (! $thresholds->absolute_enabled) {
            return true;
        }

        return formatBits(formatBytesToBits($result->download), 2, false) > $thresholds->absolute_download
            && formatBits(formatBytesToBits($result->upload), 2, false) > $thresholds->absolute_upload
            && $result->ping < $thresholds->absolute_ping;
    }
}

if (! function_exists('formatBits')) {
    function formatBits(int $bits, $precision = 2, $suffix = true)
    {
        if ($bits > 0) {
            $i = floor(log($bits) / log(1000));

            if (! $suffix) {
                return round($bits / pow(1000, $i), $precision);
            }

            $sizes = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];

            return sprintf('%.02F', round($bits / pow(1000, $i), $precision)) * 1 .' '.@$sizes[$i];
        }

        return 0;
    }
}

if (! function_exists('formatBytes')) {
    function formatBytes(int $bytes, $precision = 2)
    {
        if ($bytes > 0) {
            $i = floor(log($bytes) / log(1024));

            $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return sprintf('%.02F', round($bytes / pow(1024, $i), $precision)) * 1 .' '.@$sizes[$i];
        }

        return 0;
    }
}

if (! function_exists('formatBytesToBits')) {
    function formatBytestoBits(int $bytes)
    {
        if ($bytes > 0) {
            return $bytes * 8;
        }

        return 0;
    }
}
