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

        $schedule = $this->result->schedule;


        if (! $schedule || blank($schedule->options)) {
            return;
        }

        $preference = data_get($schedule->options, 'server_preference', 'auto');
        $preferredServers = collect(data_get($schedule->options, 'servers', []))
            ->pluck('server_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $serverId = null;

        // Handle preference: "prefer"
        if ($preference === 'prefer' && ! empty($preferredServers)) {
            $serverId = count($preferredServers) === 1
                ? $preferredServers[0]
                : Arr::random($preferredServers);
        }
        // Handle preference: "ignore"
        elseif ($preference === 'ignore' && ! empty($preferredServers)) {
            $serverId = $this->filterOutServers($preferredServers);
        }
        // Handle preference: "auto" (no server is selected)
        elseif ($preference === 'auto') {
            // No server is selected for "auto" preference.
            return;
        }

        if ($serverId) {
            $this->updateServerId($this->result, $serverId);
        } else {
            Log::warning('No suitable server found for Schedule #'.$schedule->id);
        }
    }

    private function filterOutServers(array $excluded): ?int
    {
        $servers = $this->listServers();
        // Filter out the excluded servers
        $filtered = Arr::except($servers, $excluded);

        return Arr::first($filtered);  // Return the first available server after exclusion
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

        // Return a list of servers in the format server_id => server_id
        return collect($servers)->mapWithKeys(fn (array $server) => [
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
