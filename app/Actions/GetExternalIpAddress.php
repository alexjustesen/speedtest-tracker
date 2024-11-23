<?php

namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GetExternalIpAddress
{
    use AsAction;

    public function handle(): bool|string
    {
        $externalIp = Cache::remember('external_ip', 30, function (): bool|string {
            $response = Http::retry(3, 100)
                ->get('https://icanhazip.com/');

            if ($response->failed()) {
                $message = sprintf('Failed to fetch external IP address, %d', $response->status());

                Log::warning($message);

                return false;
            }

            return Str::trim($response->body());
        });

        return $externalIp;
    }
}
