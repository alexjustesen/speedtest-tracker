<?php

namespace App\Services;

use App\Models\Result;
use Carbon\Carbon;

class DataUsageCalculator
{
    /**
     * Calculate the total data usage between two datetime points.
     */
    public static function calculate(Carbon $startDate, Carbon $endDate): array
    {
        $results = Result::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(download_bytes) as total_download_bytes, SUM(upload_bytes) as total_upload_bytes')
            ->first();

        $downloadBytes = $results->total_download_bytes ?? 0;
        $uploadBytes = $results->total_upload_bytes ?? 0;

        return [
            'download_bytes' => $downloadBytes,
            'upload_bytes' => $uploadBytes,
            'total_bytes' => $downloadBytes + $uploadBytes,
        ];
    }
}
