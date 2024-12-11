<?php

namespace App\Jobs\Ookla;

use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SelectSpeedtestServerJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        // If the server id is already set, we don't need to do anything.
        if (Arr::exists($this->result->data, 'server.id')) {
            return;
        }

        // If preferred servers are set in the config, we can use that.
        if (! blank(config('speedtest.servers'))) {
            $this->updateServerId(
                result: $this->result,
                serverId: $this->getConfigServer(),
            );

            return;
        }

        // If blocked servers config is blank, we can skip picking a server.
        if (blank(config('speedtest.blocked_servers'))) {
            return;
        }

        $serverId = $this->filterBlockedServers();

        if (blank($serverId)) {
            Log::info('Failed to select a server for Ookla speedtest, skipping blocked server filter.', [
                'result_id' => $this->result->id,
            ]);

            return;
        }

        $this->updateServerId($this->result, $serverId);
    }

    /**
     * Get a list of servers from config blocked servers.
     */
    private function getConfigBlockedServers(): array
    {
        $blocked = config('speedtest.blocked_servers');

        $blocked = array_filter(
            array_map(
                'trim',
                explode(',', $blocked),
            ),
        );

        if (blank($blocked)) {
            return [];
        }

        return collect($blocked)->mapWithKeys(function (int $serverId) {
            return [$serverId => $serverId];
        })->toArray();
    }

    /**
     * Get a server from the config servers list.
     */
    private function getConfigServer(): ?string
    {
        $servers = config('speedtest.servers');

        $servers = array_filter(
            array_map(
                'trim',
                explode(',', $servers),
            ),
        );

        return count($servers) > 0
            ? Arr::random($servers)
            : null;
    }

    /**
     * Filter servers from server list.
     */
    private function filterBlockedServers(): mixed
    {
        $blocked = $this->getConfigBlockedServers();

        $servers = $this->listServers();

        $filtered = Arr::except($servers, $blocked);

        return Arr::first($filtered);
    }

    /**
     * Get a list of servers.
     */
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
            array: json_decode($process->getOutput(), true),
            key: 'servers',
            default: [],
        );

        return collect($servers)->mapWithKeys(function (array $server) {
            return [$server['id'] => $server['id']];
        })->toArray();
    }

    /**
     * Update the result with the selected server Id.
     */
    private function updateServerId(Result $result, int $serverId): void
    {
        $result->update([
            'data->server->id' => $serverId,
        ]);
    }
}
