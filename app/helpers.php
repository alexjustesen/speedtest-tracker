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

if (! function_exists('percentChange')) {
    function percentChange(float $dividend, float $divisor, int $precision = 0): string
    {
        if ($dividend === 0 || $divisor === 0) {
            return 0;
        }

        $quotient = ($dividend - $divisor) / $divisor;

        return number_format(round($quotient * 100, $precision), $precision);
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

/**
 * Determine if the string provided is valid json.
 *
 * This function will be overwritten in php 8.3 https://wiki.php.net/rfc/json_validate
 *
 * @deprecated
 *
 * @param  string  $data
 * @return bool
 */
if (! function_exists('json_validate')) {
    function json_validate($data)
    {
        if (! empty($data)) {
            return is_string($data) &&
              is_array(json_decode($data, true)) ? true : false;
        }

        return false;
    }
}
