<?php

namespace App\Actions;

use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class GetExternalIpAddress
{
    use AsAction;

    public function handle(?string $url = null): array
    {
        $url = $url ?? app(GeneralSettings::class)->speedtest_external_ip_url;

        try {
            $response = Http::retry(3, 100)
                ->get(url: $url);
        } catch (Throwable $e) {
            $message = sprintf('Failed to fetch external IP address from "%s". See the logs for more details.', $url);

            Log::error($message, [$e->getMessage()]);

            return [
                'ok' => false,
                'body' => $message,
            ];
        }

        return [
            'ok' => $response->ok(),
            'body' => Str::of($response->body())->trim()->toString(),
        ];
    }
}
