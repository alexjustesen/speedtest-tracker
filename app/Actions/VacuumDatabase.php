<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class VacuumDatabase
{
    use AsAction;

    /**
     * Reclaim unused pages and refresh query planner stats on SQLite databases.
     * No-op for other drivers, which handle this internally.
     */
    public function handle(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'sqlite') {
            return;
        }

        // VACUUM cannot run inside a transaction. Bail out rather than fail hard
        // if one is somehow active (e.g. tests wrapped in RefreshDatabase).
        if ($connection->transactionLevel() > 0) {
            Log::warning('Skipping SQLite maintenance: active transaction detected.');

            return;
        }

        $start = microtime(true);

        $connection->statement('PRAGMA optimize;');
        $connection->statement('VACUUM;');

        Log::info('SQLite maintenance completed', [
            'duration_ms' => round((microtime(true) - $start) * 1000, 2),
        ]);
    }
}
