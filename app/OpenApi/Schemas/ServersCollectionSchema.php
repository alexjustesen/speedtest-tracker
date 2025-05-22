<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ServersCollection',
    type: 'object',
    description: 'Mapping of server IDs to display names',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'object',
            description: 'Map of server ID to display name',
            example: [
                'data' => [
                    '12345' => 'Fibernet (New York, 12345)',
                ],
            ],
        ),
        new OA\Property(
            property: 'message',
            type: 'string',
            description: 'Response status message'
        ),
    ],
    additionalProperties: false
)]
class ServersCollectionSchema {}
