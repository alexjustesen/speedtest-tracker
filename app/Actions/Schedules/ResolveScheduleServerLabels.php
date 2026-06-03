<?php

namespace App\Actions\Schedules;

use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class ResolveScheduleServerLabels
{
    use AsAction;

    /**
     * @param  array<string>  $servers
     * @param  array<string>  $blockedServers
     * @return array<string, string>
     */
    public function handle(array $servers, array $blockedServers): array
    {
        $ids = array_unique(array_merge($servers, $blockedServers));

        return collect($ids)->mapWithKeys(function ($id) {
            $label = Cache::get("ookla_server_label_{$id}");

            return $label ? [$id => $label] : [];
        })->toArray();
    }
}
