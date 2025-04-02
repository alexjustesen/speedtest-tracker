<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class CheckInternetConnection
{
    use AsAction;

    public function handle(): bool|string
    {
        try {
            $response = Http::retry(3, 100)
                ->timeout(5)
                ->get(config('speedtest.checkinternet_url'));

            if (! $response->ok()) {
                return false;
            }

            return Str::trim($response->body());
        } catch (Throwable $e) {
            Log::error('Failed to check internet connection.', [$e->getMessage()]);

            return false;
        }
    }
}
