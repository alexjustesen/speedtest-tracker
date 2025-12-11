<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class GetExternalIpAddress
{
    use AsAction;

    public function handle(?string $url = null): bool|string
    {
        try {
            $response = Http::retry(3, 100)
                ->timeout(5)
                ->get(url: config('speedtest.preflight.get_external_ip_url'));
        } catch (Throwable $e) {
            Log::error('Failed to fetch external IP address.', [$e->getMessage()]);

            return false;
        }

        return Str::trim($response->body());
    }
}
