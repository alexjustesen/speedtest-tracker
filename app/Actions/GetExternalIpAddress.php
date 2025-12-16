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

    public function handle(?string $address = null): bool|string
    {
        $address = $address ?? config('speedtest.preflight.get_external_ip_url');

        try {
            $response = Http::retry(3, 100)
                ->timeout(5)
                ->get(url: $address);
        } catch (Throwable $th) {
            Log::error('Failed to fetch external IP address from "'.$address.'".', [$th->getMessage()]);

            return false;
        }

        return Str::trim($response->body());
    }
}
