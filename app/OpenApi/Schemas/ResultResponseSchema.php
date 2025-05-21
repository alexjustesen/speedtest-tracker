<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ResultResponse',
    type: 'object',
    description: 'Envelope for a single result response',
    properties: [
        new OA\Property(
            property: 'data',
            ref: '#/components/schemas/Result'
        ),
        new OA\Property(
            property: 'message',
            type: 'string',
            description: 'Response status message',
        ),
    ]
)]
class ResultResponseSchema {}
