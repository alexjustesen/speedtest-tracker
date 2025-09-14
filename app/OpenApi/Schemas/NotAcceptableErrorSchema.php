<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NotAcceptableError',
    description: 'Error response when the Accept header is missing or invalid',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'This endpoint only accepts JSON. Please include "Accept: application/json" in your request headers.'
        ),
        new OA\Property(
            property: 'error',
            type: 'string',
            example: 'Unsupported Media Type'
        ),
    ]
)]
class NotAcceptableErrorSchema
{
}