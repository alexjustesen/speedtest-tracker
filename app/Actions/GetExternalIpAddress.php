<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

if (! config('speedtest.externalip_url')) {
    $fetch_url = 'https://icanhazip.com/';
} else {
    $fetch_url = $speedtest.externalip_url;
}

class GetExternalIpAddress
{
    use AsAction;

    public function handle(): bool|string
    {
        try {
            if (! config('speedtest.externalip_url')) {
                $fetch_url = $speedtest.externalip_url;
            } else {
                $fetch_url = "https://icanhazip.com/";
            }
            $response = Http::retry(3, 100)
                ->timeout(5)
                ->get(url: $fetch_url);
        } catch (Throwable $e) {
            Log::error('Failed to fetch external IP address.', [$e->getMessage()]);

            return false;
        }

        return Str::trim($response->body());
    }
}
