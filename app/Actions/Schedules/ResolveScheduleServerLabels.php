<?php

namespace App\Actions\Schedules;

use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class ResolveScheduleServerLabels
{
    use AsAction;

    /**
     * @param  array<string>  $servers
     * @param  array<string, string>  $existingLabels
     * @return array<string, string>
     */
    public function handle(array $servers, array $existingLabels = []): array
    {
        return collect($servers)->unique()->mapWithKeys(function ($id) use ($existingLabels) {
            $label = Cache::get("ookla_server_label_{$id}") ?? $existingLabels[$id] ?? null;

            return $label ? [$id => $label] : [];
        })->all();
    }
}
