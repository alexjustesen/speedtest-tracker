<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'QueuedResult',
    type: 'object',
    description: 'A queued speedtest result',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'service', type: 'string'),
        new OA\Property(property: 'ping', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'download', type: 'integer', nullable: true),
        new OA\Property(property: 'upload', type: 'integer', nullable: true),
        new OA\Property(property: 'benchmarks', type: 'object', nullable: true),
        new OA\Property(property: 'healthy', type: 'boolean', nullable: true),
        new OA\Property(property: 'status', type: 'string'),
        new OA\Property(property: 'scheduled', type: 'boolean'),
        new OA\Property(property: 'comments', type: 'string', nullable: true),
        new OA\Property(property: 'data', ref: '#/components/schemas/QueuedResultData'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class QueuedResultSchema {}
