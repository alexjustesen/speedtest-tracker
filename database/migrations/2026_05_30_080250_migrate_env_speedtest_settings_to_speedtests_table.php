<?php

use App\Models\Speedtest;
use App\Schedules\CronSchedule;
use Cron\CronExpression;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $this->importFromEnv(
            expression: env('SPEEDTEST_SCHEDULE'),
            servers: env('SPEEDTEST_SERVERS'),
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $ids = DB::table('speedtests')->where('name', 'Default')->pluck('id');

        DB::table('schedules')
            ->where('schedulable_type', (new Speedtest)->getMorphClass())
            ->whereIn('schedulable_id', $ids)
            ->delete();

        DB::table('speedtests')->whereIn('id', $ids)->delete();
    }

    /**
     * Create a default speedtest and cron schedule from the legacy env settings.
     */
    public function importFromEnv(?string $expression, ?string $servers): void
    {
        if (blank($expression)) {
            return;
        }

        if (! CronExpression::isValidExpression($expression)) {
            Log::warning('Skipped importing SPEEDTEST_SCHEDULE, the cron expression is invalid.', [
                'expression' => $expression,
            ]);

            return;
        }

        // Skip if a speedtest already exists to avoid duplicates on re-run.
        if (DB::table('speedtests')->exists()) {
            return;
        }

        $servers = $this->parseCommaSeparated($servers);

        $timezone = config('app.display_timezone');
        $now = now();

        DB::transaction(function () use ($expression, $servers, $timezone, $now) {
            $speedtestId = DB::table('speedtests')->insertGetId([
                'name' => 'Default',
                'server_mode' => $servers ? 'prefer' : 'auto',
                'servers' => $servers ? json_encode($servers) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('schedules')->insert([
                'schedulable_type' => (new Speedtest)->getMorphClass(),
                'schedulable_id' => $speedtestId,
                'type' => 'cron',
                'expression' => $expression,
                'timezone' => $timezone,
                'next_run_at' => (new CronSchedule($expression, $timezone))->getNextOccurrence($now),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    /**
     * Parse a comma-separated env string into an array, or null if blank.
     */
    private function parseCommaSeparated(?string $value): ?array
    {
        if (blank($value)) {
            return null;
        }

        $items = array_values(array_filter(array_map('trim', explode(',', $value))));

        return empty($items) ? null : $items;
    }
};
