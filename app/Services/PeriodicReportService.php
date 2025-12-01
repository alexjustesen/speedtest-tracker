<?php

namespace App\Services;

use App\Enums\ResultStatus;
use App\Helpers\Number;
use App\Models\Result;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PeriodicReportService
{
    public function getResults(Carbon $start, Carbon $end): Collection
    {
        return Result::query()
            ->whereBetween('created_at', [$start, $end])
            ->get();
    }

    public function calculateStats(Collection $results): array
    {
        return [
            'download_avg' => Number::toBitRate(bits: $results->avg('download') * 8, precision: 2),
            'upload_avg' => Number::toBitRate(bits: $results->avg('upload') * 8, precision: 2),
            'ping_avg' => round($results->avg('ping'), 2).' ms',
            'total_tests' => $results->count(),
            'successful_tests' => $results->where('status', ResultStatus::Completed)->count(),
            'failed_tests' => $results->where('status', ResultStatus::Failed)->count(),
            'healthy_tests' => $results->where('healthy', '===', true)->count(),
            'unhealthy_tests' => $results->where('healthy', '===', false)->count(),
        ];
    }

    public function calculateServerStats(Collection $results): Collection
    {
        return $results
            ->where('status', '===', ResultStatus::Completed)
            ->groupBy('server_name')
            ->map(function ($serverResults) {
                return [
                    'server_name' => $serverResults->first()->server_name ?? 'Unknown',
                    'count' => $serverResults->count(),
                    'download_avg' => Number::toBitRate(bits: $serverResults->avg('download') * 8, precision: 2),
                    'upload_avg' => Number::toBitRate(bits: $serverResults->avg('upload') * 8, precision: 2),
                    'ping_avg' => round($serverResults->avg('ping'), 2).' ms',
                ];
            })
            ->values()
            ->sortByDesc('count');
    }
}
