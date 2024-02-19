<?php

namespace App\Http\Controllers\API\Speedtest;

use App\Enums\ResultStatus;
use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\JsonResponse;

class GetLatestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): JsonResponse
    {
        $latest = Result::query()
            ->whereIn('status', [ResultStatus::Completed, ResultStatus::Failed])
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
                'download' => $latest->download_bits,
                'upload' => $latest->upload_bits,
                'server_id' => $latest->server_id,
                'server_host' => $latest->server_host,
                'server_name' => $latest->server_name,
                'url' => $latest->result_url,
                'scheduled' => $latest->scheduled,
                'failed' => $latest->status === ResultStatus::Failed,
                'created_at' => $latest->created_at->toISOString(true),
                'updated_at' => $latest->updated_at->toISOString(true),
            ],
        ]);
    }
}
