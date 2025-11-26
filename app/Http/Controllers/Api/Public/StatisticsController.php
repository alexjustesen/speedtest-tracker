<?php

namespace App\Http\Controllers\Api\Public;

use App\Enums\ResultStatus;
use App\Http\Controllers\Controller;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * Get statistics for a specific metric (download, upload, ping).
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
            ->where('status', ResultStatus::Completed);

        // Apply time range filter
        $query->where('created_at', '>=', $this->getStartDate($timeRange));

        // Apply server filter if provided
        if ($serverId) {
            $query->where('server_id', $serverId);
        }

        // Calculate statistics for the metric
        $stats = $query->selectRaw("
            MAX({$metric}) as highest,
            MIN({$metric}) as lowest,
            AVG({$metric}) as average
        ")->first();

        // Get latest value (build fresh query to avoid GROUP BY conflicts)
        $latestQuery = Result::query()
            ->where('status', ResultStatus::Completed)
            ->where('created_at', '>=', $this->getStartDate($timeRange));

        if ($serverId) {
            $latestQuery->where('server_id', $serverId);
        }

        $latest = $latestQuery->latest()->value($metric);

        return response()->json([
            'metric' => $metric,
            'latest' => $latest ? round($latest, 2) : null,
            'average' => $stats->average ? round($stats->average, 2) : null,
            'lowest' => $stats->lowest ? round($stats->lowest, 2) : null,
            'highest' => $stats->highest ? round($stats->highest, 2) : null,
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
