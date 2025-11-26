<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ResultsCollection',
    type: 'object',
    description: 'Paginated list of Speedtest results',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            description: 'Array of result objects',
            items: new OA\Items(ref: '#/components/schemas/Result')
        ),
        new OA\Property(
            property: 'links',
            type: 'object',
            properties: [
                new OA\Property(property: 'first', type: 'string'),
                new OA\Property(property: 'last', type: 'string'),
                new OA\Property(property: 'prev', type: 'string', nullable: true),
                new OA\Property(property: 'next', type: 'string', nullable: true),
            ],
            additionalProperties: false
        ),
        new OA\Property(
            property: 'meta',
            type: 'object',
            properties: [
                new OA\Property(property: 'current_page', type: 'integer'),
                new OA\Property(property: 'from', type: 'integer'),
                new OA\Property(property: 'last_page', type: 'integer'),
                new OA\Property(
                    property: 'links',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'url', type: 'string', nullable: true),
                            new OA\Property(property: 'label', type: 'string'),
                            new OA\Property(property: 'active', type: 'boolean'),
                        ],
                        additionalProperties: false
                    )
                ),
                new OA\Property(property: 'path', type: 'string'),
                new OA\Property(property: 'per.page', type: 'integer'),
                new OA\Property(property: 'to', type: 'integer'),
                new OA\Property(property: 'total', type: 'integer'),
            ],
            additionalProperties: false
        ),
    ],
    additionalProperties: false
)]
class ResultsCollectionSchema {}