<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class GetOoklaSpeedtestServers
{
    use AsAction;

    public function handle(): array
    {
        $query = [
            'engine' => 'js',
            'https_functional' => true,
            'limit' => 20,
        ];

        try {
            $response = Http::retry(3, 250)
                ->timeout(5)
                ->get(url: 'https://www.speedtest.net/api/js/servers', query: $query);
        } catch (Throwable $e) {
            Log::error('Unable to retrieve Ookla servers.', [$e->getMessage()]);

            return [
                '⚠️ Unable to retrieve Ookla servers, check internet connection and see logs.',
            ];
        }

        return $response->collect()->mapWithKeys(function (array $item, int $key) {
            return [
                $item['id'] => $item['sponsor'].' ('.$item['name'].', '.$item['id'].')',
            ];
        })->toArray();
    }
}
