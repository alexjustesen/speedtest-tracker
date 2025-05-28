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
        return collect(self::fetch())->mapWithKeys(function (array $item) {
            return [
                $item['id'] => ($item['sponsor'] ?? 'Unknown').' ('.($item['name'] ?? 'Unknown').', '.$item['id'].')',
            ];
        })->toArray();
    }

    /**
     * Fetch the raw Ookla server array from the Ookla API.
     */
    public static function fetch(): array
    {
        $query = [
            'engine' => 'js',
            'https_functional' => true,
            'limit' => 20,
        ];

        try {
            $response = Http::retry(3, 250)
                ->timeout(5)
                ->get('https://www.speedtest.net/api/js/servers', $query);

            return $response->json();
        } catch (Throwable $e) {
            Log::error('Unable to retrieve Ookla servers.', [$e->getMessage()]);

            return [
                '⚠️ Unable to retrieve Ookla servers, check internet connection and see logs.',
            ];
        }
    }

    /**
     * For API: return array of structured server objects
     */
    public static function forApi(): array
    {
        return collect(self::fetch())->map(function (array $item) {
            return [
                'id' => $item['id'],
                'host' => $item['host'] ?? null,
                'name' => ($item['sponsor'] ?? 'Unknown'),
                'location' => $item['name'] ?? 'Unknown',
                'country' => $item['country'] ?? 'Unknown',
            ];
        })->toArray();
    }
}
