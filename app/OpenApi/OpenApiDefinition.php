<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: 'Speedtest Tracker API',
        version: '1.0.0',
    ),
    components: new OA\Components(
        securitySchemes: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'JWT'
            ),
        ],
        parameters: [
            new OA\Parameter(
                name: 'Accept',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string', default: 'application/json'),
                description: 'Expected response format'
            ),
        ]
    ),
    tags: [
        new OA\Tag(
            name: 'Results',
            description: 'Endpoints for accessing and filtering speedtest results. Requires API token with `results:read` scope.'
        ),
        new OA\Tag(
            name: 'Speedtests',
            description: 'Endpoints for initiating speedtests and retrieving available servers. Requires `speedtests:run` or `speedtests:read` token scopes.'
        ),
        new OA\Tag(
            name: 'Stats',
            description: 'Endpoints for retrieving aggregated statistics and performance metrics. Requires `speedtests:read` token scope.'
        ),
        new OA\Tag(
            name: 'Application',
            description: 'Endpoints for application-level operations such as health checks. No authentication required.'
        ),
    ]
)]
class OpenApiDefinition {}
