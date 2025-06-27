<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\StatResource;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;
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
            ->allowedFilters([
                AllowedFilter::operator(name: 'start_at', internalName: 'created_at', filterOperator: FilterOperator::DYNAMIC),
                AllowedFilter::operator(name: 'end_at', internalName: 'created_at', filterOperator: FilterOperator::DYNAMIC),
            ])
            ->first();

        // Return wrapped in a resource
        return $this->sendResponse(
            data: new StatResource($stats)
        );
    }
}
