<?php

if (! function_exists('formatBytes')) {
    function formatBytes(int $bytes, $precision = 2)
    {
        $base = log($bytes, 1024);
        $suffixes = array('', 'Kbps', 'Mbps', 'Gbps', 'Tbps');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
}
