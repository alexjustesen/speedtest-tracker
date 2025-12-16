<?php

namespace App\Services\Network;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GetExternalIpAddress
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public ?string $url = null,
    ) {}

    /**
     * Invoke the class instance.
     */
    public function __invoke(): bool|string
    {
        $response = Http::retry(3, 100)
            ->timeout(5)
            ->throw(function (Response $response, RequestException $e) {
                Log::error('Failed to fetch external IP address from "'.$this->url.'".', [$e->getMessage()]);

                return false;
            })
            ->get(url: config('speedtest.preflight.get_external_ip_url'));

        return Str::trim($response->body());
    }
}
