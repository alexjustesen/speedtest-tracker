<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ResultsCollection',
    type: 'object',
    description: 'Paginated list of Results',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            description: 'Array of result objects',
            items: new OA\Items(ref: '#/components/schemas/Result')
        ),
        new OA\Property(
            property: 'meta',
            ref: '#/components/schemas/PaginationMeta'
        ),
        new OA\Property(
            property: 'links',
            ref: '#/components/schemas/PaginationLinks'
        ),
    ]
)]
class ResultsCollectionSchema {}
