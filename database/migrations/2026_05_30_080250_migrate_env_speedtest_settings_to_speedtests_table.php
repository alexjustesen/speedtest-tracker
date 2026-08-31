<?php

use App\Models\Speedtest;
use DirectoryTree\Cadence\Drivers\CronSchedule;
use Illuminate\Database\Migrations\Migration;

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

        // Skip if a speedtest already exists to avoid duplicates on re-run.
        if (Speedtest::query()->exists()) {
            return;
        }

        $speedtest = Speedtest::create([
            'name' => 'Default',
            'servers' => $this->parseCommaSeparated(env('SPEEDTEST_SERVERS')),
            'blocked_servers' => $this->parseCommaSeparated(env('SPEEDTEST_BLOCKED_SERVERS')),
            'interface' => env('SPEEDTEST_INTERFACE') ?: null,
            'skip_ips' => $this->parseCommaSeparated(env('SPEEDTEST_SKIP_IPS')),
        ]);

        $speedtest->addSchedule(new CronSchedule($cronSchedule, config('app.display_timezone')));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Speedtest::where('name', 'Default')->get()->each->delete();
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
