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
        $data['server_labels'] = ResolveScheduleServerLabels::run(
            servers: $data['servers'] ?? [],
            blockedServers: $data['blocked_servers'] ?? [],
        );

        return $data;
    }
}
