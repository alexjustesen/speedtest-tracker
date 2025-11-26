<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\JsonResponse;

class ServersController extends Controller
{
    /**
     * Get list of servers from speedtest results.
     */
    public function __invoke(): JsonResponse
    {
        $servers = Result::query()
            ->select('server_id', 'server_name')
            ->selectRaw('COUNT(*) as test_count')
            ->whereNotNull('server_id')
            ->groupBy('server_id', 'server_name')
            ->orderByDesc('test_count')
            ->get();

        return response()->json($servers);
    }
}
