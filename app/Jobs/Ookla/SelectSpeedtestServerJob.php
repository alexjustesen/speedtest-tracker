<?php

namespace App\Jobs\Ookla;

use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SelectSpeedtestServerJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(public Result $result) {}

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
        ];
    }

    public function handle(): void
    {
        if (Arr::get($this->result->data, 'server.id')) {
            return;
        }

        $test = $this->result->test;

        if (!$test || blank($test->options)) {
            return;
        }

        $preference = data_get($test->options, 'server_preference', 'auto');
        $preferredServers = collect(data_get($test->options, 'servers', []))
            ->pluck('server_id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $serverId = null;

        if ($preference === 'prefer' && !empty($preferredServers)) {
            $serverId = Arr::random($preferredServers);
        } elseif ($preference === 'ignore' && !empty($preferredServers)) {
            $serverId = $this->filterOutServers($preferredServers);
        } elseif ($preference === 'auto') {
            $serverId = $this->getAnyServer();
        }

        if ($serverId) {
            $this->updateServerId($this->result, $serverId);
        } else {
            Log::warning('No suitable server found for Test #' . $test->id);
        }
    }

    private function getAnyServer(): ?int
    {
        $servers = array_keys($this->listServers());
        return Arr::random($servers);
    }

    private function filterOutServers(array $excluded): ?int
    {
        $servers = $this->listServers();
        $filtered = Arr::except($servers, $excluded);
        return Arr::first($filtered);
    }

    private function listServers(): array
    {
        $command = [
            'speedtest',
            '--accept-license',
            '--accept-gdpr',
            '--servers',
            '--format=json',
        ];

        $process = new Process($command);

        try {
            $process->run();
        } catch (ProcessFailedException $e) {
            Log::error('Failed listing Ookla speedtest servers.', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }

        $servers = Arr::get(
            json_decode($process->getOutput(), true),
            'servers',
            []
        );

        return collect($servers)->mapWithKeys(fn(array $server) => [
            $server['id'] => $server['id'],
        ])->toArray();
    }

    private function updateServerId(Result $result, int $serverId): void
    {
        $result->update([
            'data->server->id' => $serverId,
        ]);
    }
}
