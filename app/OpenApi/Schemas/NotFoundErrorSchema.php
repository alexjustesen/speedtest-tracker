<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NotFoundError',
    type: 'object',
    description: 'Error when a requested result is not found',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            description: 'Result not found error message',
            example: 'Result not found.',
        ),
    ],
)]
class NotFoundErrorSchema {}
