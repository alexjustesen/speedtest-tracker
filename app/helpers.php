<?php

use ChrisUllyott\FileSize;

if (! function_exists('convertSize')) {
    /**
     * Using FileSize convert bytes to the output format with precision.
     */
    function convertSize(float $input, string $output = 'MB', int $precision = 4): float
    {
        $size = new FileSize($input, 10);

        return (float) $size->as($output, $precision);
    }
}

if (! function_exists('toBits')) {
    /**
     * Takes a byte based float and transforms it into bits with precision.
     */
    function toBits(float $size, int $precision = 4): float
    {
        return (float) number_format(($size * 8), $precision, '.', '');
    }
}

if (! function_exists('absoluteDownloadThresholdFailed')) {
    function absoluteDownloadThresholdFailed(float $threshold, float $download = 0): bool
    {
        return toBits(convertSize($download), 2) < $threshold;
    }
}

if (! function_exists('absoluteUploadThresholdFailed')) {
    function absoluteUploadThresholdFailed(float $threshold, float $upload = 0): bool
    {
        return toBits(convertSize($upload), 2) < $threshold;
    }
}

if (! function_exists('absolutePingThresholdFailed')) {
    function absolutePingThresholdFailed(float $threshold, float $ping): bool
    {
        return $ping > $threshold;
    }
}
