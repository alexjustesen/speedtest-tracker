<?php

namespace App\OpenApi\Annotations;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class AppAnnotations
{
    #[OA\Get(
        path: '/api//healthcheck',
        summary: 'App Healthcheck',
        description: 'Simple liveness check for Speedtest Tracker.',
        operationId: 'healthcheck',
        tags: ['Application'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        ),
                    ]
                )
            ),
        ]
    )]
    public function healthcheck(): void {}
}
