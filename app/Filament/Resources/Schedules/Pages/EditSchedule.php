<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
