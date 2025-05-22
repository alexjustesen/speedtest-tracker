<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NotFoundError',
    type: 'object',
    description: 'Error when a requested when the reqeusted data is not found',
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            description: 'Not found error message',
        ),
    ],
)]
class NotFoundErrorSchema {}
