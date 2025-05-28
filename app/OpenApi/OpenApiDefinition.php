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
            description: 'Endpoints for retrieving speedtest results. Requires token scope `results:read`.'
        ),
        new OA\Tag(
            name: 'Speedtests',
            description: 'Endpoints for running speedtests and listing servers. Requires token scopes `speedtests:run` and/or `speedtests:read`.'
        ),
        new OA\Tag(
            name: 'Stats',
            description: 'Endpoints for viewing performance statistics. Requires token scope `speedtests:read`.'
        ),
    ]
)]
class OpenApiDefinition {}
