<?php

namespace App\OpenApi\Annotations\V1;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: '/api/v1/about',
    description: 'Endpoint for retrieving application information.'
)]
class AboutAnnotations
{
    #[OA\Get(
        path: '/api/v1/about',
        summary: 'Retrieve application information',
        description: 'Returns the application name, build version and build date. Requires an authenticated API token.',
        operationId: 'getAbout',
        tags: ['About'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/AcceptHeader'),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Application information retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'name', type: 'string', example: 'Speedtest Tracker'),
                                new OA\Property(property: 'build_version', type: 'string', example: 'v1.14.3'),
                                new OA\Property(property: 'build_date', type: 'string', format: 'date', example: '2026-05-25'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'message', type: 'string', example: 'ok'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_ACCEPTABLE,
                description: 'Not Acceptable - Missing or invalid Accept header',
                content: new OA\JsonContent(ref: '#/components/schemas/NotAcceptableError')
            ),
        ]
    )]
    public function about(): void {}
}
