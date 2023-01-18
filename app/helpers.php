<?php

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

if (! function_exists('roundBytesToMegabits')) {
    function roundBytesToMegabits(int $bytes)
    {
        if ($bytes > 0) {
            $bits = formatBytesToBits($bytes);
            $megabit_exponent = 2;
            $precision = 2;

            return round($bits / pow(1000, $megabit_exponent), $precision);
        }

        return 0;
    }
}

if (! function_exists('percentChange')) {
    function percentChange(float $dividend, float $divisor, int $precision = 0): string
    {
        $quotient = ($dividend - $divisor) / $divisor;

        return number_format(round($quotient * 100, $precision), $precision);
    }
}

if (! function_exists('absoluteDownloadThresholdFailed')) {
    function absoluteDownloadThresholdFailed(float $threshold, float $download): bool
    {
        return formatBits(formatBytesToBits($download), 2, false) < $threshold;
    }
}

if (! function_exists('absoluteUploadThresholdFailed')) {
    function absoluteUploadThresholdFailed(float $threshold, float $upload): bool
    {
        return formatBits(formatBytesToBits($upload), 2, false) < $threshold;
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
 * @return  bool
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
