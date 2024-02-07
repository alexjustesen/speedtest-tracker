<?php

namespace App\Actions;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

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

        $response = Http::retry(3, 250)
            ->timeout(5)
            ->get(url: 'https://www.speedtest.net/api/js/servers', query: $query)
            ->throw(function (Response $response, RequestException $e) {
                Log::error($e);

                return [
                    '0' => 'There was an issue retrieving Ookla speedtest servers, check the logs for more info.',
                ];
            })
            ->collect();

        return $response->mapWithKeys(function (array $item, int $key) {
            return [
                $item['id'] => $item['id'].': '.$item['name'].' ('.$item['sponsor'].')',
            ];
        })->toArray();
    }
}
