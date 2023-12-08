<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class TimeZoneHelper
{
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
}
