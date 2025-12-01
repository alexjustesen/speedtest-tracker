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
        $completedResults = $results->where('status', ResultStatus::Completed);

        // Calculate packet loss stats
        $packetLossValues = $completedResults->pluck('packet_loss')->filter(fn ($value) => is_numeric($value));
        $packetLossAvg = $packetLossValues->isNotEmpty() ? round($packetLossValues->avg(), 2).'%' : 'N/A';
        $packetLossMax = $packetLossValues->isNotEmpty() ? round($packetLossValues->max(), 2).'%' : 'N/A';
        $packetLossMin = $packetLossValues->isNotEmpty() ? round($packetLossValues->min(), 2).'%' : 'N/A';

        return [
            'download_avg' => Number::toBitRate(bits: $results->avg('download') * 8, precision: 2),
            'upload_avg' => Number::toBitRate(bits: $results->avg('upload') * 8, precision: 2),
            'ping_avg' => round($results->avg('ping'), 2).' ms',
            'download_max' => $completedResults->isNotEmpty() ? Number::toBitRate(bits: $completedResults->max('download') * 8, precision: 2) : 'N/A',
            'download_min' => $completedResults->isNotEmpty() ? Number::toBitRate(bits: $completedResults->min('download') * 8, precision: 2) : 'N/A',
            'upload_max' => $completedResults->isNotEmpty() ? Number::toBitRate(bits: $completedResults->max('upload') * 8, precision: 2) : 'N/A',
            'upload_min' => $completedResults->isNotEmpty() ? Number::toBitRate(bits: $completedResults->min('upload') * 8, precision: 2) : 'N/A',
            'ping_max' => $completedResults->isNotEmpty() ? round($completedResults->max('ping'), 2).' ms' : 'N/A',
            'ping_min' => $completedResults->isNotEmpty() ? round($completedResults->min('ping'), 2).' ms' : 'N/A',
            'packet_loss_avg' => $packetLossAvg,
            'packet_loss_max' => $packetLossMax,
            'packet_loss_min' => $packetLossMin,
            'total_tests' => $results->count(),
            'successful_tests' => $completedResults->count(),
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
