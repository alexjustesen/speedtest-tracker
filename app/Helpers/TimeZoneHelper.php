<?php

namespace App\Helpers;

use DateTimeZone;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TimeZoneHelper
{
    /**
     * Returns a collection of time zones with their offset from UTC.
     */
    public static function list()
    {
        $seconds = 3600; // 1hr

        return Cache::remember('timezones_list_collection', $seconds, function () {
            $timestamp = time();

            foreach (timezone_identifiers_list() as $key => $value) {
                date_default_timezone_set($value);

                $timezone[$value] = $value.' (UTC '.date('P', $timestamp).')';
            }

            return collect($timezone)->sortKeys();
        });
    }

    /**
     * Validates the time zone string provided.
     *
     * Ref: https://github.com/laravel/framework/blob/10.x/src/Illuminate/Validation/Concerns/ValidatesAttributes.php#L2406-L2420
     */
    public static function validate($value, $parameters = [])
    {
        return in_array($value, timezone_identifiers_list(
            constant(DateTimeZone::class.'::'.Str::upper($parameters[0] ?? 'ALL')),
            isset($parameters[1]) ? Str::upper($parameters[1]) : null,
        ), true);
    }
}
