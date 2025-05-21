<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    security: [['bearerAuth' => []]]
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'yourtokengoeshere'
)]
#[OA\Parameter(
    name: 'Accept',
    in: 'header',
    required: true,
    schema: new OA\Schema(type: 'string', default: 'application/json'),
    description: 'Expected response format'
)]
class OpenApiSpec {}
