<?php

namespace App\OpenApi\Annotations\V1;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Speedtests',
    description: 'Endpoints for running speedtests.'
)]
class SpeedtestAnnotations
{
    #[OA\Post(
        path: '/api/v1/speedtests/run',
        summary: 'Run a new Ookla speedtest',
        operationId: 'runSpeedtest',
        tags: ['Speedtests'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/AcceptHeader'),
            new OA\Parameter(
                name: 'server_id',
                in: 'query',
                description: 'Optional Ookla speedtest server ID',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Created',
                content: new OA\JsonContent(ref: '#/components/schemas/SpeedtestRun')
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
    public function run(): void
    {
        // Annotation placeholder for runSpeedtest
    }
}
