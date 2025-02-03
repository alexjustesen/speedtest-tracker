<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\StatResource;
use App\Models\Result;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class Stats extends ApiController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
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
            ->AllowedFilters([
                AllowedFilter::operator(name: 'start_at', internalName: 'created_at', filterOperator: FilterOperator::DYNAMIC),
                AllowedFilter::operator(name: 'end_at', internalName: 'created_at', filterOperator: FilterOperator::DYNAMIC),
            ])
            ->first();

        return self::sendResponse(
            data: new StatResource($stats),
            filters: $request->input('filter'),
        );
    }
}
