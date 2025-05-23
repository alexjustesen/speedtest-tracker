<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ApiTokenEdit',
    type: 'object',
    description: 'Response returned when an API token is edited',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'object',
            description: 'Updated API token entry',
            properties: [
                new OA\Property(property: 'id', type: 'integer', description: 'Token database ID'),
                new OA\Property(property: 'name', type: 'string', description: 'User-assigned name for this token'),
                new OA\Property(
                    property: 'scopes',
                    type: 'array',
                    description: 'Permissions granted to this token',
                    items: new OA\Items(type: 'string')
                ),
                new OA\Property(
                    property: 'expires_at',
                    type: 'string',
                    format: 'date-time',
                    nullable: true,
                    description: 'Expiration timestamp or null if none'
                ),
            ]
        ),
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'Token scopes updated.'
        ),
    ]
)]
class ApiTokenEditSchema {}
