<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ServersCollection',
    type: 'object',
    description: 'Collection of Ookla speedtest servers',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            description: 'List of server objects',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'string'),
                    new OA\Property(property: 'host', type: 'string'),
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'location', type: 'string'),
                    new OA\Property(property: 'country', type: 'string'),
                ]
            )
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
