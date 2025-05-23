<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ApiTokenCreated',
    type: 'object',
    description: 'Response returned when a new API token is issued',
    properties: [
        new OA\Property(
            property: 'token',
            type: 'string',
            description: 'The issued API token string',
        ),
        new OA\Property(
            property: 'scopes',
            type: 'array',
            description: 'List of permissions granted to this token',
            items: new OA\Items(type: 'string'),
        ),
        new OA\Property(
            property: 'expires_at',
            type: 'string',
            format: 'date-time',
            nullable: true,
            description: 'Timestamp when the token expires',
        ),
    ]
)]
class ApiTokenCreatedSchema {}
