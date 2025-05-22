<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Stats',
    type: 'object',
    description: 'Aggregated speedtest statistics',
    properties: [
        new OA\Property(property: 'total_results', type: 'integer'),
        new OA\Property(property: 'avg_ping', type: 'number', format: 'float'),
        new OA\Property(property: 'avg_download', type: 'number', format: 'float'),
        new OA\Property(property: 'avg_upload', type: 'number', format: 'float'),
        new OA\Property(property: 'min_ping', type: 'number', format: 'float'),
        new OA\Property(property: 'min_download', type: 'number', format: 'float'),
        new OA\Property(property: 'min_upload', type: 'number', format: 'float'),
        new OA\Property(property: 'max_ping', type: 'number', format: 'float'),
        new OA\Property(property: 'max_download', type: 'number', format: 'float'),
        new OA\Property(property: 'max_upload', type: 'number', format: 'float'),
    ]
)]
class StatsSchema {}
