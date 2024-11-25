<?php

namespace App\Jobs\Ookla;

use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;

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

        $serverId = $this->result->server_id
            ?? $this->getConfigServer();

        if ($this->result->server_id != $serverId) {
            $this->result->update([
                'data->server->id' => $serverId,
            ]);
        }
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
}
