<?php

namespace App\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class GetOoklaSpeedtestServers
{
    use AsAction;

    /**
     * For UI: return the ID, Sponsor, and Name to start a manual test
     */
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
                ->get(url: 'https://www.speedtest.net/api/js/servers', query: $query);

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
        $servers = self::fetch();

        // If the first item is not an array, treat as error or empty
        if (empty($servers) || ! is_array($servers) || (isset($servers[0]) && ! is_array($servers[0]))) {
            // Optionally, you could return an error message here, but to match the controller's behavior, return an empty array
            return [];
        }

        return collect($servers)->map(function (array $item) {
            return [
                'id' => $item['id'],
                'host' => Arr::get($item, 'host', 'Unknown'),
                'name' => Arr::get($item, 'sponsor', 'Unknown'),
                'location' => Arr::get($item, 'name', 'Unknown'),
                'country' => Arr::get($item, 'country', 'Unknown'),
            ];
        })->toArray();
    }
}
