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
        $failedResults = $results->where('status', ResultStatus::Failed);
        $healthyResults = $results->where('healthy', '===', true);
        $unhealthyResults = $results->where('healthy', '===', false);

        $hasCompletedResults = $completedResults->isNotEmpty();

        // Calculate packet loss stats
        $packetLossValues = $completedResults->pluck('packet_loss')->filter(fn ($value) => is_numeric($value));
        $hasPacketLoss = $packetLossValues->isNotEmpty();

        return [
            'download_avg' => Number::toBitRate(bits: $results->avg('download') * 8, precision: 2),
            'upload_avg' => Number::toBitRate(bits: $results->avg('upload') * 8, precision: 2),
            'ping_avg' => round($results->avg('ping'), 2).' ms',
            'download_max' => $hasCompletedResults ? Number::toBitRate(bits: $completedResults->max('download') * 8, precision: 2) : 'N/A',
            'download_min' => $hasCompletedResults ? Number::toBitRate(bits: $completedResults->min('download') * 8, precision: 2) : 'N/A',
            'upload_max' => $hasCompletedResults ? Number::toBitRate(bits: $completedResults->max('upload') * 8, precision: 2) : 'N/A',
            'upload_min' => $hasCompletedResults ? Number::toBitRate(bits: $completedResults->min('upload') * 8, precision: 2) : 'N/A',
            'ping_max' => $hasCompletedResults ? round($completedResults->max('ping'), 2).' ms' : 'N/A',
            'ping_min' => $hasCompletedResults ? round($completedResults->min('ping'), 2).' ms' : 'N/A',
            'packet_loss_avg' => $hasPacketLoss ? round($packetLossValues->avg(), 2).'%' : 'N/A',
            'packet_loss_max' => $hasPacketLoss ? round($packetLossValues->max(), 2).'%' : 'N/A',
            'packet_loss_min' => $hasPacketLoss ? round($packetLossValues->min(), 2).'%' : 'N/A',
            'total_tests' => $results->count(),
            'successful_tests' => $completedResults->count(),
            'failed_tests' => $failedResults->count(),
            'healthy_tests' => $healthyResults->count(),
            'unhealthy_tests' => $unhealthyResults->count(),
        ];
    }
}
