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

        if (Arr::exists($this->result->data, 'server.id')) {
            Log::info('Server exists for speedtest, skipping select server job.');

            return;
        }

        $serverId = null;

        if (! blank(config('speedtest.servers'))) {
            $serverId = $this->getConfigServer();

            $this->updateServerId($this->result, $serverId);

            return;
        }

        $serverId = $this->filterServers()

        $serverId = $this->result->server_id
            ?? $this->getConfigServer();

        $this->result->update([
            'data->server->id' => $serverId,
        ]);
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
    private function filterServers()
    {
        $blocked = config('speedtest.blocked_servers');

        $blocked = array_filter(
            array_map(
                'trim',
                explode(',', $blocked),
            ),
        );

        $servers = $this->listServers();

        $filtered = Arr::except($servers, $blocked);


    }

    /**
     * Get a list of servers.
     */
    private function listServers(): ?array
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

        return json_decode($process->getOutput(), true);
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
