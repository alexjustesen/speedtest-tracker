<?php

namespace App\Actions;

use App\Settings\GeneralSettings;
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
                ->get(app(GeneralSettings::class)->speedtest_checkinternet_url);

            if (! $response->ok()) {
                return false;
            }

            return Str::trim($response->body());
        } catch (Throwable $e) {
            Log::error('Failed to connect to the internet.', [$e->getMessage()]);

            return false;
        }
    }
}
