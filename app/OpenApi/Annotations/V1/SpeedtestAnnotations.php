<?php

namespace App\OpenApi\Annotations\V1;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Speedtests',
    description: 'Endpoints for running speedtests and listing servers.'
)]
class SpeedtestAnnotations
{
    #[OA\Post(
        path: '/api/v1/speedtests/run',
        summary: 'Run a new Ookla speedtest',
        operationId: 'runSpeedtest',
        tags: ['Speedtests'],
        parameters: [
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

    #[OA\Get(
        path: '/api/v1/speedtests/list-servers',
        summary: 'List available Ookla speedtest servers',
        operationId: 'listSpeedtestServers',
        tags: ['Speedtests'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/ServersCollection')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError',
                    example: ['message' => 'You do not have permission to view speedtest servers.']
                )
            ),
        ]
    )]
    public function listServers(): void {}
}
