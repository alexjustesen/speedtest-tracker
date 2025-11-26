<?php

namespace App\Http\Controllers\Api\Public;

use App\Enums\ResultStatus;
use App\Http\Controllers\Controller;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChartDataController extends Controller
{
    /**
     * Get chart data for a specific metric (download, upload, ping).
     */
    public function __invoke(Request $request, string $metric): JsonResponse
    {
        // Validate metric parameter
        if (! in_array($metric, ['download', 'upload', 'ping'])) {
            return response()->json([
                'error' => 'Invalid metric. Must be one of: download, upload, ping',
            ], 400);
        }

        $timeRange = $request->query('time_range', '24h');
        $serverId = $request->query('server');

        $query = Result::query()
            ->select(['id', $metric, 'created_at'])
            ->where('status', ResultStatus::Completed);

        // Apply time range filter
        $query->where('created_at', '>=', $this->getStartDate($timeRange));

        // Apply server filter if provided
        if ($serverId) {
            $query->where('server_id', $serverId);
        }

        // Get results ordered by date
        $results = $query->orderBy('created_at')->get();

        // Calculate average
        $average = $results->avg($metric);

        // Format data for charts
        $data = $results->map(function ($result) use ($metric) {
            return [
                'x' => $result->created_at->toIso8601String(),
                'y' => $result->$metric ? round($result->$metric, 2) : null,
            ];
        })->values();

        return response()->json([
            'metric' => $metric,
            'data' => $data,
            'average' => $average ? round($average, 2) : null,
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
