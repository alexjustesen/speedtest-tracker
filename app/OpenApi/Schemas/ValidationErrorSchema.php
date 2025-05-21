<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    description: 'Validation failed due to invalid server_id input',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            description: 'Validation failed due to invalid server_id input',
        ),
    ]
)]

class ValidationErrorSchema {}
