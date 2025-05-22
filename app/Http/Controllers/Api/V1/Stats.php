<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\StatResource;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Schema as OASchema;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class Stats extends ApiController
{
    /**
     * Handle the incoming request.
     */
    #[OA\Get(
        path: '/api/v1/stats',
        summary: 'Fetch aggregated Speedtest statistics',
        operationId: 'getStats',
        tags: ['Stats'],
        parameters: [
            new OA\Parameter(
                name: 'start_at',
                in: 'query',
                description: 'ISO‑8601 start datetime filter',
                required: false,
                schema: new OASchema(type: 'string', format: 'date-time')
            ),
            new OA\Parameter(
                name: 'end_at',
                in: 'query',
                description: 'ISO‑8601 end datetime filter',
                required: false,
                schema: new OASchema(type: 'string', format: 'date-time')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/Stats')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation failed',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
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
