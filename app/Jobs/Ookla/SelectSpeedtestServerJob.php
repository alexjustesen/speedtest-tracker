<?php

namespace App\Jobs\Ookla;

use App\Enums\ServerMode;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
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

        $speedtest = $this->result->speedtest;

        // Preferred servers only apply to scheduled tests.
        if (! $this->result->scheduled
            || $speedtest?->server_mode !== ServerMode::Prefer
            || blank($speedtest->servers)) {
            return;
        }

        $this->updateServerId($this->result, Arr::random($speedtest->servers));
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
