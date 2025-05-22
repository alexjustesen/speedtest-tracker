<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ResultResponse',
    type: 'object',
    description: 'Response for an Single Speedtest result entry',
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
