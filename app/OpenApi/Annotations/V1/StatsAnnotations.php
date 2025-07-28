<?php

namespace App\OpenApi\Annotations\V1;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: '/api/v1/stats',
    description: 'Endpoints for viewing performance statistics.'
)]
class StatsAnnotations
{
    #[OA\Get(
        path: '/api/v1/stats',
        summary: 'Fetch aggregated Speedtest statistics',
        operationId: 'getStats',
        tags: ['Stats'],
        parameters: [
            new OA\Parameter(
                name: 'start_at',
                in: 'query',
                description: 'Filter stats from this date/time (ISO 8601)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
            new OA\Parameter(
                name: 'end_at',
                in: 'query',
                description: 'Filter stats up to this date/time (ISO 8601)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Statistics fetched successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Stats')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function getStats(): void {}
}
