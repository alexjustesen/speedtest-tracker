<?php

namespace App\OpenApi\Support;

class OoklaExamples
{
    public const LIST = [
        'data' => [
            [
                'id' => 12345,
                'host' => 'speedtest.example.com',
                'port' => 8080,
                'name' => 'Example ISP',
                'location' => 'Amsterdam',
                'country' => 'Netherlands',
                'ip' => '192.0.2.1',
            ],
            [
                'id' => 23456,
                'host' => 'speedtest2.example.com',
                'port' => 8080,
                'name' => 'Another ISP',
                'location' => 'Rotterdam',
                'country' => 'Netherlands',
                'ip' => '198.51.100.2',
            ],
        ],
        'message' => 'Speedtest servers fetched successfully.',
    ];

    public const FORBIDDEN = [
        'data' => null,
        'message' => 'You do not have permission to view speedtest servers.',
    ];
}
