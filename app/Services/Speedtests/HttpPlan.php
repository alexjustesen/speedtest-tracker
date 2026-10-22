<?php

namespace App\Services\Speedtests;

/**
 * Endpoints and metadata for one run of an HTTP based speedtest.
 */
class HttpPlan
{
    public function __construct(
        public string $latencyUrl,
        public array $downloadUrls,
        public array $uploadUrls = [],
        public int $uploadChunk = 4_000_000,
        public bool $rangedLatency = false,
        public array $server = [],
        public ?string $isp = null,
        public ?string $externalIp = null,
        public array $headers = [],
    ) {}
}
