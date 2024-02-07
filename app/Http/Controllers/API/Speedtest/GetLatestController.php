<?php

namespace App\Http\Controllers\API\Speedtest;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\JsonResponse;

class GetLatestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * TODO: fix it
     */
    public function __invoke(): JsonResponse
    {
        $latest = Result::query()
            ->latest()
            ->first();

        if (! $latest) {
            return response()->json([
                'message' => 'No results found.',
            ], 404);
        }

        return response()->json([
            'message' => 'ok',
            'data' => [
                'id' => $latest->id,
                'ping' => $latest->ping,
                'download' => ! blank($latest->download) ? toBits(convertSize($latest->download)) : null,
                'upload' => ! blank($latest->upload) ? toBits(convertSize($latest->upload)) : null,
                'server_id' => $latest->server_id,
                'server_host' => $latest->server_host,
                'server_name' => $latest->server_name,
                'url' => $latest->url,
                'scheduled' => $latest->scheduled,
                'failed' => ! $latest->successful,
                'created_at' => $latest->created_at->toISOString(true),
                'updated_at' => $latest->created_at->toISOString(true), // faking updated at to match legacy api payload
            ],
        ]);
    }
}
