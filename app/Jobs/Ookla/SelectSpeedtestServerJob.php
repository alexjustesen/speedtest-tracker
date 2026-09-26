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

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
    ) {}

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // If the server id is already set, we don't need to do anything.
        if (Arr::get($this->result->data, 'server.id')) {
            return;
        }

        // If preferred servers are set on the schedule, use that, but only if the test is scheduled.
        $preferredServers = $this->result->speedtest?->servers ?? [];

        if ($this->result->scheduled && ! blank($preferredServers)) {
            $this->updateServerId(
                result: $this->result,
                serverId: Arr::random($preferredServers),
            );

            return;
        }

        // If blocked servers are blank, we can skip picking a server.
        $blockedServers = $this->result->speedtest?->blocked_servers ?? [];

        if (blank($blockedServers)) {
            return;
        }

        $serverId = $this->filterBlockedServers($blockedServers);

        if (blank($serverId)) {
            Log::info('Failed to select a server for Ookla speedtest, skipping blocked server filter.', [
                'result_id' => $this->result->id,
            ]);

            return;
        }

        $this->updateServerId($this->result, $serverId);
    }

    /**
     * Filter servers from server list, excluding blocked ones.
     *
     * @param  array<int|string>  $blockedServers
     */
    private function filterBlockedServers(array $blockedServers): mixed
    {
        $blocked = collect($blockedServers)->mapWithKeys(fn ($id) => [$id => $id])->toArray();

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
