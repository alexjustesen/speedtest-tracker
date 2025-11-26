<?php

namespace App\Http\Controllers\Api\Public;

use App\Enums\ResultStatus;
use App\Http\Controllers\Controller;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    /**
     * Get health monitoring data (success rate and status).
     */
    public function __invoke(Request $request): JsonResponse
    {
        $timeRange = $request->query('time_range', '24h');
        $serverId = $request->query('server');

        $query = Result::query()
            ->where('created_at', '>=', $this->getStartDate($timeRange));

        // Apply server filter if provided
        if ($serverId) {
            $query->where('server_id', $serverId);
        }

        // Get total count
        $total = $query->count();

        // Get completed count
        $completed = (clone $query)->where('status', ResultStatus::Completed)->count();

        // Get failed count
        $failed = (clone $query)->where('status', ResultStatus::Failed)->count();

        // Calculate health percentage
        $percentage = $total > 0 ? round(($completed / $total) * 100, 1) : null;

        // Get latest test status
        $latestResult = Result::query()
            ->where('created_at', '>=', $this->getStartDate($timeRange))
            ->when($serverId, fn ($q) => $q->where('server_id', $serverId))
            ->latest()
            ->first();

        $latestStatus = $latestResult ? $latestResult->status->value : null;

        return response()->json([
            'percentage' => $percentage,
            'status' => $latestStatus,
            'total' => $total,
            'completed' => $completed,
            'failed' => $failed,
        ]);
    }

    /**
     * Get start date based on time range.
     */
    protected function getStartDate(string $timeRange): Carbon
    {
        return match ($timeRange) {
            '24h' => now()->subHours(24),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subHours(24),
        };
    }
}
