<?php

namespace App\Actions;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GetExternalIpAddress
{
    use AsAction;

    public function handle(?string $address = null): bool|string
    {
        $address = $address ?? config('speedtest.preflight.get_external_ip_url');

        $response = Http::retry(3, 100)
            ->timeout(5)
            ->throw(function (Response $response, RequestException $e) use ($address) {
                Log::error('Failed to fetch external IP address from "'.$address.'".', [$e->getMessage()]);

                return false;
            })
            ->get(url: $address);

        return Str::trim($response->body());
    }
}
