<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\StatResource;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;

class StatsController extends ApiController
{
    /**
     * GET /api/v1/stats
     * Fetch aggregated Speedtest statistics with optional start/end filters.
     */
    public function __invoke(Request $request)
    {
        if ($request->user()->tokenCant('results:read')) {
            return $this->sendResponse(
                data: null,
                message: 'You do not have permission to view statistics.',
                code: Response::HTTP_FORBIDDEN
            );
        }

        $validator = Validator::make($request->all(), [
            'filter.start_at' => 'sometimes|date',
            'filter.end_at' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(
                data: $validator->errors(),
                message: 'Validation failed.',
                code: 422
            );
        }

        // Build the stats query
        $stats = QueryBuilder::for(Result::class)
            ->selectRaw('count(*) as total_results')
            ->selectRaw('avg(ping) as avg_ping')
            ->selectRaw('avg(download) as avg_download')
            ->selectRaw('avg(upload) as avg_upload')
            ->selectRaw('min(ping) as min_ping')
            ->selectRaw('min(download) as min_download')
            ->selectRaw('min(upload) as min_upload')
            ->selectRaw('max(ping) as max_ping')
            ->selectRaw('max(download) as max_download')
            ->selectRaw('max(upload) as max_upload')
            ->allowedFilters($this->dateRangeFilters())
            ->first();

        // Return wrapped in a resource
        return $this->sendResponse(
            data: new StatResource($stats)
        );
    }
}
