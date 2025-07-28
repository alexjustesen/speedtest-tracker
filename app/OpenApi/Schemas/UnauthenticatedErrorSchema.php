<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UnauthenticatedError',
    type: 'object',
    description: 'Error when user is not authenticated',
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            description: 'Unauthenticated error message',
        ),
    ],
)]
class UnauthenticatedErrorSchema {}
