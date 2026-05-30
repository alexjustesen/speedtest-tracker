<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['server_labels'] = $this->resolveServerLabels(
            servers: $data['servers'] ?? [],
            blockedServers: $data['blocked_servers'] ?? [],
        );

        return $data;
    }

    private function resolveServerLabels(array $servers, array $blockedServers): array
    {
        $ids = array_unique(array_merge($servers, $blockedServers));

        return collect($ids)->mapWithKeys(function ($id) {
            $label = Cache::get("ookla_server_label_{$id}");

            return $label ? [$id => $label] : [];
        })->toArray();
    }
}
