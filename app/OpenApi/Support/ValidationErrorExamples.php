<?php

namespace App\OpenApi\Support;

class ValidationErrorExamples
{
    public const PER_PAGE = [
        'data' => [
            'per_page' => [
                'The per page must be at least 1.',
            ],
        ],
        'message' => 'Validation failed.',
    ];
}
