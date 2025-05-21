<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'QueuedResultDataServer',
    type: 'object',
    description: 'Server info inside queued result data',
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            format: 'int64',
            nullable: true
        ),
    ]
)]
class QueuedResultDataServerSchema {}
