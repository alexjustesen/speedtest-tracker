<?php

namespace App\OpenApi\Annotations\V1;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: '/api/v1/ookla',
    description: 'Endpoints for retrieving Ookla speedtest servers and related resources.'
)]
class OoklaAnnotations
{
    #[OA\Get(
        path: '/api/v1/ookla/list-servers',
        summary: 'List available Ookla speedtest servers',
        description: 'Returns an array of available Ookla speedtest servers. Requires an API token with `ookla:list-servers` scope.',
        operationId: 'listOoklaServers',
        tags: ['Servers'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/AcceptHeader'),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Servers retrieved successfully',
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
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_ACCEPTABLE,
                description: 'Not Acceptable - Missing or invalid Accept header',
                content: new OA\JsonContent(ref: '#/components/schemas/NotAcceptableError')
            ),
        ]
    )]
    public function listServers(): void {}
}
