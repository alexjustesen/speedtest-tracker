<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ServersCollection',
    type: 'object',
    description: 'Mapping of server IDs to display names',
    required: ['data', 'message'],
    properties: [
        new OA\Property(
            property: 'data',
            type: 'object',
            description: 'Map of server ID to server display name',
            additionalProperties: true,
            items: null,
            example: [
                '20001' => 'Washington Fiber (Washington, DC, 20001)',
                '90001' => 'LA Connect (Los Angeles, CA, 90001)',
                '60601' => 'ChiTown Broadband (Chicago, IL, 60601)',
                '77001' => 'Houston Net (Houston, TX, 77001)',
                '33101' => 'MiamiLink (Miami, FL, 33101)',
            ]
        ),
        new OA\Property(
            property: 'message',
            type: 'string',
            description: 'Response status message',
            example: 'Speedtest servers fetched successfully.'
        ),
    ]
)]
class ServersCollectionSchema {}
