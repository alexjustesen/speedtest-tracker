<?php

namespace App\OpenApi\Annotations\V1;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: '/api/v1/version',
    description: 'Endpoint for retrieving application version information.'
)]
class VersionAnnotations
{
    #[OA\Get(
        path: '/api/v1/version',
        summary: 'Get application version and update information',
        description: 'Returns the currently installed application version and checks for available updates. Requires admin:read token ability.',
        operationId: 'getVersion',
        tags: ['Version'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/AcceptHeader'),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Version information retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Version')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden - Missing admin:read token ability',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_ACCEPTABLE,
                description: 'Not Acceptable - Missing or invalid Accept header',
                content: new OA\JsonContent(ref: '#/components/schemas/NotAcceptableError')
            ),
        ]
    )]
    public function getVersion(): void {}
}
