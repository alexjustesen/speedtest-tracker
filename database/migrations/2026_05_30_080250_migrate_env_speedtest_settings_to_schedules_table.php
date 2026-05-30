<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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

        $cronSchedule = env('SPEEDTEST_SCHEDULE');

        if (blank($cronSchedule)) {
            return;
        }

        // Skip if a schedule already exists to avoid duplicates on re-run.
        if (DB::table('schedules')->exists()) {
            return;
        }

        DB::table('schedules')->insert([
            'name' => 'Default',
            'enabled' => true,
            'schedule' => $cronSchedule,
            'servers' => $this->parseCommaSeparated(env('SPEEDTEST_SERVERS')),
            'blocked_servers' => $this->parseCommaSeparated(env('SPEEDTEST_BLOCKED_SERVERS')),
            'interface' => env('SPEEDTEST_INTERFACE') ?: null,
            'skip_ips' => $this->parseCommaSeparated(env('SPEEDTEST_SKIP_IPS')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('schedules')->where('name', 'Default')->delete();
    }

    /**
     * Parse a comma-separated env string into a JSON array, or null if blank.
     */
    private function parseCommaSeparated(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $items = array_values(array_filter(array_map('trim', explode(',', $value))));

        return empty($items) ? null : json_encode($items);
    }
};
