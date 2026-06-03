<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Actions\Schedules\ResolveScheduleServerLabels;
use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $serverMode = $data['server_mode'] ?? 'auto';
        unset($data['server_mode']);

        if ($serverMode !== 'prefer') {
            $data['servers'] = null;
        }

        if ($serverMode !== 'block') {
            $data['blocked_servers'] = null;
        }

        $data['server_labels'] = ResolveScheduleServerLabels::run(
            servers: $data['servers'] ?? [],
            blockedServers: $data['blocked_servers'] ?? [],
        );

        return $data;
    }
}
