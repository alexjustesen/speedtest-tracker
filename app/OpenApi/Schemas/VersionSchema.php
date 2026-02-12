<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Version',
    title: 'Version',
    description: 'Application version and update information',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'object',
            properties: [
                new OA\Property(
                    property: 'app',
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'version',
                            type: 'string',
                            example: 'v1.13.7'
                        ),
                        new OA\Property(
                            property: 'build_date',
                            type: 'string',
                            format: 'date-time',
                            example: '2026-02-04T00:00:00+00:00'
                        ),
                    ]
                ),
                new OA\Property(
                    property: 'updates',
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'latest_version',
                            type: 'string',
                            nullable: true,
                            example: 'v1.13.8'
                        ),
                        new OA\Property(
                            property: 'update_available',
                            type: 'boolean',
                            example: true
                        ),
                    ]
                ),
            ]
        ),
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'ok'
        ),
    ]
)]
class VersionSchema {}
