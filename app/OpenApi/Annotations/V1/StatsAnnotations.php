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
            new OA\Parameter(ref: '#/components/parameters/AcceptHeader'),
            new OA\Parameter(
                name: 'filter[start_at]',
                in: 'query',
                description: 'Filter stats created on or after this date/time (alias for created_at>=)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
            new OA\Parameter(
                name: 'filter[end_at]',
                in: 'query',
                description: 'Filter stats created on or before this date/time (alias for created_at<=). A date without a time includes the entire day.',
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
                response: Response::HTTP_NOT_ACCEPTABLE,
                description: 'Not Acceptable - Missing or invalid Accept header',
                content: new OA\JsonContent(ref: '#/components/schemas/NotAcceptableError')
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
