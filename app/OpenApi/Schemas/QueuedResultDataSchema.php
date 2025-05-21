<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'QueuedResultData',
    type: 'object',
    description: 'Additional data for a queued result',
    properties: [
        new OA\Property(
            property: 'server',
            ref: '#/components/schemas/QueuedResultDataServer'
        ),
    ]
)]
class QueuedResultDataSchema {}
