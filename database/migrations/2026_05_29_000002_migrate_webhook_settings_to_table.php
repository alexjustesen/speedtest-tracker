<?php

use App\Enums\WebhookEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Move webhooks previously stored in the notification settings into the
     * dedicated webhooks table. The original settings rows are intentionally
     * left in place to go stale.
     */
    public function up(): void
    {
        $enabled = (bool) $this->setting('webhook_enabled', false);
        $onSpeedtestRun = (bool) $this->setting('webhook_on_speedtest_run', false);
        $onThresholdFailure = (bool) $this->setting('webhook_on_threshold_failure', false);
        $urls = $this->setting('webhook_urls', []) ?? [];

        $events = array_values(array_filter([
            $onSpeedtestRun ? WebhookEvent::Completed->value : null,
            $onThresholdFailure ? WebhookEvent::BenchmarkUnhealthy->value : null,
        ]));

        foreach ($urls as $row) {
            $url = $row['url'] ?? null;

            if (blank($url)) {
                continue;
            }

            DB::table('webhooks')->insert([
                'url' => $url,
                'events' => json_encode($events),
                'enabled' => $enabled,
                'secret' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $urls = $this->setting('webhook_urls', []) ?? [];

        $urls = array_values(array_filter(array_map(
            fn ($row) => $row['url'] ?? null,
            $urls,
        )));

        if (count($urls)) {
            DB::table('webhooks')->whereIn('url', $urls)->delete();
        }
    }

    /**
     * Read a notification setting value directly from the settings table.
     */
    private function setting(string $name, mixed $default = null): mixed
    {
        $row = DB::table('settings')
            ->where('group', 'notification')
            ->where('name', $name)
            ->value('payload');

        if ($row === null) {
            return $default;
        }

        return json_decode($row, true) ?? $default;
    }
};
