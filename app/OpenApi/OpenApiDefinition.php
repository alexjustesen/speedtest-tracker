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
        new OA\Tag(name: 'Results', description: "Operations related to Speedtest results.\nRequires an API token with scope `results:read`."),
        new OA\Tag(name: 'Speedtests', description: "Operations to run speedtests.\nRequires an API token with scope `speedtests:run`."),
        new OA\Tag(name: 'Servers', description: "Operations for speedtest servers.\nRequires an API token with scope `ookla:list-servers`."),
        new OA\Tag(name: 'Stats', description: "Operations for statistics.\nRequires an API token with scope `results:read`."),
    ]
)]
class OpenApiDefinition {}
