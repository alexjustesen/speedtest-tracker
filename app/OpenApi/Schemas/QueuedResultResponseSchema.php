<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'QueuedResultResponse',
    type: 'object',
    description: 'Response when a speedtest is queued',
    required: ['data', 'message'],
    properties: [
        new OA\Property(
            property: 'data',
            ref: '#/components/schemas/QueuedResult'
        ),
        new OA\Property(
            property: 'message',
            type: 'string',
            description: 'Response status message',
            example: 'Speedtest added to the queue.'
        ),
    ]
)]
class QueuedResultResponseSchema {}
