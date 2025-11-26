<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ServersController extends Controller
{
    /**
     * Get list of servers from speedtest results.
     */
    public function __invoke(): JsonResponse
    {
        $cacheKey = 'dashboard_v2_servers';
        $cacheTtl = config('speedtest.public_api.servers_cache_ttl', 600);

        $servers = Cache::remember($cacheKey, $cacheTtl, function () {
            return Result::query()
                ->select('server_id', 'server_name')
                ->selectRaw('COUNT(*) as test_count')
                ->whereNotNull('server_id')
                ->groupBy('server_id', 'server_name')
                ->orderByDesc('test_count')
                ->get();
        });

        return response()->json($servers);
    }
}
