<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ForbiddenError',
    type: 'object',
    description: 'Forbidden error response when user lacks permission',
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            description: 'Error message indicating lack of permission',
        ),
    ]
)]
class ForbiddenErrorSchema {}
