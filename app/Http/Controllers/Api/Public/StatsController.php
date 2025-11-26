<?php

namespace App\Http\Controllers\Api\Public;

use App\Enums\ResultStatus;
use App\Http\Controllers\Controller;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    /**
     * Get latest speedtest result with optional filtering.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $timeRange = $request->query('time_range', '24h');
        $serverId = $request->query('server');

        $cacheKey = 'dashboard_v2_stats_'.$timeRange.'_'.($serverId ?: 'all');
        $cacheTtl = config('speedtest.public_api.stats_cache_ttl', 60);

        $latestResult = Cache::remember($cacheKey, $cacheTtl, function () use ($timeRange, $serverId) {
            $query = Result::query()
                ->select(['id', 'ping', 'download', 'upload', 'server_id', 'server_name', 'created_at'])
                ->where('status', ResultStatus::Completed);

            // Apply time range filter
            $query->where('created_at', '>=', $this->getStartDate($timeRange));

            // Apply server filter if provided
            if ($serverId) {
                $query->where('server_id', $serverId);
            }

            return $query->latest()->first();
        });

        return response()->json($latestResult);
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
